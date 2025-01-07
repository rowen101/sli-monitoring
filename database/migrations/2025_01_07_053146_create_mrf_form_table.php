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
        Schema::create('mrf_form', function (Blueprint $table) {
            $table->id();
            $table->string('mrf_order_number')->nullable();
            $table->integer('site_id');
            $table->string('requisitioner');
            $table->date('date_requested'); 
            $table->date('date_needed');
            $table->string('purpose');
            $table->enum('status', ['P', 'A', 'C'])->default('P');
             $table->binary('prepared_by_signature')->nullable(); 
             $table->integer('noted_by')->nullable();
             $table->binary('noted_by_signature')->nullable(); 
            $table->integer('created_by')->nullable(0);
            $table->integer('updated_by')->nullable(0);
            $table->timestamps();
        });

        Schema::create('mrf_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mrf_form_id')->constrained('mrf_form')->onDelete('cascade');
            $table->string('particulars'); 
            $table->string('description'); 
            $table->integer('quantity'); 
            $table->string('uom');
            $table->decimal('unit_price', 8, 2);
            $table->decimal('total_amount', 10, 2);
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
        Schema::dropIfExists('mrf_form');
        Schema::dropIfExists('mrf_items');
    }
};
