<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet_type extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'min_balance',
        'monthly_interest_rate'
    ];

    public function wallet()
    {
        return $this->hasMany(wallet::class, 'wallet_type_id', 'id');
    }
}
