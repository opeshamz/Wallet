<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\User;
use App\Helper\Response;
use App\Services\WalletService;
use App\Http\Requests\UserRegistrationRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Contracts\Validation\Validator;


class AuthController extends Controller
{
    //
    public function register(UserRegistrationRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request['name'],
                'email' => $request['email'],
                'phone_number' => $request['phone_number'],
                'password' => Hash::make($request['password']),

            ]);
            // Wallet::create([
            //     'user_id' => $user->id,
            //     'currency' => 'NGN',
            //     'balance' => 0,
            //     'balance_before' => 0,
            //     'balance_after' => 0,
            //     'ledger_balance' => 0,
            //     'wallet_type_id' => 1,
            // ]);
            WalletService::createWallet($user->id, 'NGN', 1);
            return Response::success('User created successfully', $user,  201);
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request['email'])->with('wallet')->first();
        if ($user) {
            if (Hash::check($request['password'], $user->password)) {
                $success['token'] = $user->createToken('MyApp')->plainTextToken;
                $respons = [
                    'token' => $success['token'],
                    'user' => $user,
                ];
                return Response::success('User logged in successfully',  $respons,  200);
            } else {
                return Response::error('Invalid credentials', 401);
            }
        } else {
            return Response::error('User not found', 404);
        }
    }
}
