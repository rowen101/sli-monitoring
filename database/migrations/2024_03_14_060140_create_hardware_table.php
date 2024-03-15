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
        Schema::create('hardware', function (Blueprint $table) {
            $table->id();
            $table->string('brand');
            $table->string('model');
            $table->string('serial');
            $table->string('os_name')->nullable();
            $table->string('os_version')->nullable();
            $table->string('type');
            $table->string('ram')->nullable();
            $table->string('cpu')->nullable();

            $table->string('status');
            $table->boolean('current')->default(true);

            $table->string('company_id')->index()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->index()->constrained()->nullOnDelete();
            $table->foreignId('provaider_id')->index()->constrained()->cascadeOnDelete();

            $table->dateTime('purchased_at');
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
        Schema::dropIfExists('hardware');
    }
};
