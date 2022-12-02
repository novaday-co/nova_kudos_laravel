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
        Schema::create('participate_group_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id');
            $table->foreign('group_id')->references('id')->on('groups')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('event_id');
            $table->foreign('event_id')->references('id')->on('events')->onUpdate('cascade')->onDelete('cascade');

            $table->tinyInteger('award')->default(0)->comment('0 => loser, 1 => winner');
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
        Schema::dropIfExists('participate_group_events');
    }
};
