<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Wallet;
use App\Models\Wallet_type;
use App\Models\user_transactions;
use App\Helper\Response;
use App\Helper\Utils;
use Illuminate\Support\Facades\Log;


class WalletService
{
    public static function createWallet(int $user_id, string $currency, int $type)
    {
        $wallet = Wallet::create([
            'user_id' => $user_id,
            'currency' => $currency,
            'balance' => 0,
            'balance_before' => 0,
            'balance_after' => 0,
            'ledger_balance' => 0,
            'wallet_type_id' => $type,
        ]);
        return $wallet;
    }
    public static function getAllWallets()
    {
        $wallets = Wallet::all();
        return $wallets;
    }

    public static function walletToWallet(int $id, float $amount, string $description, int $initiatedTo_id)
    {
        $initiatedTo_id = Wallet::where('user_id', $initiatedTo_id)->first();
        $wallet = Wallet::where('id', $id)->with('wallet_type')->first();
        if (!$initiatedTo_id) {
            return Response::error('Invalid wallet account number', 400);
        }
        if ($wallet->balance === $wallet->wallet_type->min_amount) {
            return Response::error('minimum amount is same as balance', 400);
        }
        if ($amount > $wallet->balance) {
            return Response::error('Insufficient balance', 400);
        }
        try {
            DB::beginTransaction();
            // debit wallet
            $wallet->balance_before = $wallet->balance;
            $wallet->balance -= $amount;
            $wallet->balance_after = $wallet->balance;
            $wallet->ledger_balance -= $amount;
            $wallet->save();
            $reference = strtoupper(Utils::ranString(15));
            $debit = user_transactions::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'fee' => '20',
                'type' => 'DEBIT',
                'status' => 'SUCCESS',
                'reference' =>  $reference,
                'description' => $description,
                'initiatedBy_id' => $wallet->user_id,
                'initiatedTo_id' => $initiatedTo_id->user_id,
            ]);
            // credit wallet
            $initiatedTo_id->balance_before = $initiatedTo_id->balance;
            $initiatedTo_id->balance += $amount;
            $initiatedTo_id->balance_after = $initiatedTo_id->balance;
            $initiatedTo_id->ledger_balance += $amount;
            $initiatedTo_id->save();
            $credit = user_transactions::create([
                'wallet_id' => $initiatedTo_id->id,
                'amount' => $amount,
                'fee' => 20,
                'type' => 'CREDIT',
                'status' => 'SUCCESS',
                'reference' =>  $reference,
                'description' => $description,
                'initiatedBy_id' => $wallet->user_id,
                'initiatedTo_id' => $initiatedTo_id->user_id,
            ]);
            DB::commit();
            $response = [
                "debit" => $debit,
                "credit" => $credit,
            ];
            return Response::success('Transaction successful', $response, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }

    public static function getAllWalletTypes()
    {
        $wallet_types = Wallet_type::all();
        return $wallet_types;
    }
    public static function fundWallet(int $id, float $amount)
    {
        $wallet = Wallet::where('id', $id)->first();
        if (!$wallet) {
            return Response::error('Invalid wallet account number', 400);
        }
        try {
            DB::beginTransaction();
            // credit wallet
            $wallet->balance_before = $wallet->balance;
            $wallet->balance += $amount;
            $wallet->balance_after = $wallet->balance;
            $wallet->ledger_balance += $amount;
            $wallet->save();
            $credit = user_transactions::create([
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'fee' => '20',
                'type' => 'CREDIT',
                'status' => 'SUCCESS',
                'reference' => strtoupper(Utils::ranString(11)),
                'description' => 'Fund wallet',
                'initiatedBy_id' => $wallet->user_id,
                'initiatedTo_id' => $wallet->user_id,
            ]);
            DB::commit();
            $response = [
                "wallet" => $wallet,
                "credit" => $credit,
            ];
            return Response::success('Transaction successful', $response, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
