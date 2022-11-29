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
        Schema::create('gift_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_id');
            $table->foreign('from_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('to_id');
            $table->foreign('to_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('gift_id');
            $table->foreign('gift_id')->references('id')->on('gift_cards')->onUpdate('cascade')->onDelete('cascade');

            $table->text('message')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gift_users');
    }
};
