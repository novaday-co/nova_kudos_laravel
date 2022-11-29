<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('from_id')->nullable();
            $table->foreign('from_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('to_id')->nullable();
            $table->foreign('to_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('gift_id')->nullable();
            $table->foreign('gift_id')->references('id')->on('gift_cards')->onUpdate('cascade')->onDelete('cascade');

            $table->tinyInteger('type')->default(0)->comment('0 => gift, 1 => birth day, 2 => event');

            $table->foreignId('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('amount');
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
        Schema::dropIfExists('transactions');
    }
};
