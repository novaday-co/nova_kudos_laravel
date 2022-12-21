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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('mobile', 64)->unique();
            $table->integer('login_count')->default(0);
            $table->boolean('super_admin')->default(0)->comment('0 => user, 1=> super admin');

            $table->string('otp_code')->nullable();
            $table->dateTime('expiration_otp')->nullable();
            $table->dateTime('activation_date')->nullable();
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
        Schema::dropIfExists('users');
    }
};
