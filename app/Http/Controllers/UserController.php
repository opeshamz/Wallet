<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;
use App\Models\Wallet_type;
use App\Models\user_transactions;
use App\Services\WalletService;
use App\Helper\Response;
use Illuminate\Http\Request;
use App\Http\Requests\WalletRequest;
use App\Http\Requests\WalletToWalletRequest;
use App\Http\Requests\SetPinRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
    //
    public function index()
    {
        $users = User::all();
        return Response::success('Users retrieved successfully', $users,  200);
    }
    public function getAllWallets()
    {
        $wallet = WalletService::getAllWallets();
        return Response::success('Wallets retrieved successfully', $wallet,  200);
    }
    public function createWallet(WalletRequest $request)
    {

        $user = $request->user();
        $wallet = WalletService::createWallet($user->id, 'NGN', $request->wallet_type_id);
        return Response::success('Wallet created successfully', $wallet,  201);
    }

    public function getUserDetailsAndWallets(Request $request)
    {
        $user = $request->user();
        $wallets = Wallet::where('user_id', $user->id)->get();
        $user_details = [
            'user' => $user,
            'wallets' => $wallets,
        ];
        return Response::success('User details retrieved successfully', $user_details,  200);
    }
    public function walletToWallet(WalletToWalletRequest $request)
    {
        $user = $request->user();
        $data = User::where('id', $user->id)->first();
        $check_pin = Hash::check($request->pin, $data->pin);
        if (!$check_pin) {
            return Response::error('Invalid transaction pin', 401);
        }
        $wallet = WalletService::WalletToWallet($request['from'], $request['amount'], $request['description'], $request['destination']);
        return  $wallet;
    }

    public function userWalletTransaction(Request $request)
    {
        $user = $request->user();
        $user = User::where('id', $user->id)->with('wallet')->with('transaction')->first();
        if (!$user) {
            return Response::success('User not found', [],  200);
        }
        return Response::success('User wallet transaction retrieved successfully', $user,  200);
    }

    public function walletUserTransactionByWalletId(Request $request, int $id)
    {
        //$id = $request->query('id');
        $wallet = Wallet::where('id', $id)->with('user')->with('wallet_type')->with('transaction')->first();
        if (!$wallet) {
            return Response::success('Wallet not found', [],  200);
        }
        return Response::success('Wallet user transaction retrieved successfully', $wallet,  200);
    }
    public function countSummary(Request $request)
    {
        // $user = $request->user();
        $users = User::count();
        $wallet_count = Wallet::count();
        $wallet_sum = Wallet::sum('balance');
        $transaction_volume = user_transactions::sum('amount');
        $resounse = [
            'users' => $users,
            'wallets_count' => $wallet_count,
            'wallet_sum' => $wallet_sum,
            'transaction_volume' => $transaction_volume,
        ];
        return Response::success(
            'User count, Wallet count, Wallet sum, Transaction volume retrieved successfully',
            $resounse,
            200
        );
    }
    public function getWalletType()
    {
        $wallet_types = WalletService::getAllWalletTypes();
        return Response::success('Wallet types retrieved successfully', $wallet_types,  200);
    }
    public function fundWallet(Request $request)
    {
        // $user = $request->user();
        $wallet = WalletService::fundWallet($request['to'], $request['amount']);
        return $wallet;
    }

    public function setPin(SetPinRequest $request)
    {
        $user = $request->user();
        $user->pin = Hash::make($request->pin);
        $user->save();
        return Response::success('Pin set successfully', $user,  200);
    }
}
