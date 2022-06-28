<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_transactions extends Model
{
    use HasFactory;
    protected $fillable = [
        'wallet_id',
        'amount',
        'fee',
        'type',
        'status',
        'reference',
        'description',
        'initiatedBy_id',
        'initiatedTo_id',
    ];
    public  function wallet()
    {
        return $this->belongsTo(wallet::class);
    }

    public function initiatedBy()
    {
        return $this->belongsTo(User::class, 'initiatedBy_id');
    }

    public function initiatedTo()
    {
        return $this->belongsTo(User::class, 'initiatedTo_id');
    }
}
