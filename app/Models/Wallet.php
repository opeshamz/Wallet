<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'balance',
        'balance_before',
        'balance_after',
        'ledger_balance',
        'currency',
        'wallet_type_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function transaction()
    {
        return $this->hasMany(user_transactions::class, 'wallet_id', 'id');
    }

    public function wallet_type()
    {
        return $this->belongsTo(Wallet_type::class, 'wallet_type_id', 'id');
    }
}
