<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('techrecomms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('recommnum',20);
            $table->string('company',150);
            $table->string('branch',20);
            $table->string('department',20);
            $table->string('warehouse')->nullable();
            $table->string('user',20);
            $table->string('brand',20);
            $table->string('model',50)->nullable();
            $table->string('assettag',50)->nullable();
            $table->string('serialnum',50)->nullable();
            $table->text('problem');
            $table->text('assconducted');
            $table->text('recommendation');
            $table->tinyInteger('status');
            $table->integer('created_by')->nullable(0);
            $table->integer('updated_by')->nullable(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('techrecomms');
    }
};
