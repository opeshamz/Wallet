<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id');
            $table->foreign('wallet_id')->references('id')->on('wallets');
            $table->decimal('amount', 18, 2)->default(0.0);
            $table->string('fee');
            $table->string('type');
            $table->string('status');
            $table->string('reference');
            $table->string('description');
            $table->unsignedBigInteger('initiatedBy_id');
            $table->foreign('initiatedBy_id')->references('id')->on('users');
            $table->unsignedBigInteger('initiatedTo_id');
            $table->foreign('initiatedTo_id')->references('id')->on('users');
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
        Schema::dropIfExists('user_transactions');
    }
}
