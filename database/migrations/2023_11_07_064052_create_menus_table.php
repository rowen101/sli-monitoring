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
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('menu_id');
            $table->string('menu_title', 100)->index();
            $table->integer('parent_id')->default(0);
            $table->string('menu_icon')->nullable();
            $table->string('menu_route', 50)->deafault('default')->index()->nullable(); // url
            $table->integer('sort_order')->default(100)->nullable();
            $table->boolean('is_active')->default(true)->nullable();
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('menus');
    }
};
