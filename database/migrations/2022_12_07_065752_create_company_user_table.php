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
        Schema::create('company_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');

            $table->string('first_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('job_position', 255)->nullable();
            $table->string('avatar', 255)->nullable();

            $table->integer('coin_amount')->default(0);
            $table->integer('currency_amount')->default(0);
            $table->integer('notification_unread')->default(0);

            $table->foreignId('is_default')->nullable();
            $table->foreign('is_default')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('role_id')->nullable();
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('company_users');
    }
};
