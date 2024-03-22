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
        Schema::create('pallets', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('site_id')->references('id')->on('tbl_sites');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->date('date');
            $table->integer('allocatedpalletspace');
            $table->integer('spaceuteltotal');
            $table->integer('caseperpallet');
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
        Schema::dropIfExists('pallets');
    }
};
