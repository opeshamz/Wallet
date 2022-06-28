<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('wallet_type_id');
            $table->foreign('wallet_type_id')->references('id')->on('wallet_types');
            $table->decimal('balance', 18, 2)->default(0.0);
            $table->decimal('balance_before', 18, 2)->default(0.0);
            $table->decimal('balance_after', 18, 2)->default(0.0);
            $table->decimal('ledger_balance', 18, 2)->default(0.0);
            $table->string('currency')->default('NGN');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}
