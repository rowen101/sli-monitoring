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
        Schema::create('job_maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('job_order_number')->nullable();
            $table->integer('site_id');
            $table->string('end_user')->nullable();
            $table->timestamp('time_requested')->nullable();
            $table->date('date_needed')->nullable();
            $table->string('noted_by')->nullable();
            $table->enum('type_of_job', ['Preventive Maintenance', 'Corrective Maintenance', 'Calibration'])->nullable();
            $table->text('problem_description')->nullable();
            $table->text('findings_recommendations')->nullable();
            $table->date('commitment_date')->nullable();
            $table->tinyInteger('status');
            $table->integer('created_by')->nullable(0);
            $table->integer('updated_by')->nullable(0);
            $table->timestamps();

        });
        Schema::create('job_replacement_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_order_request_id')->constrained('job_maintenances')->onDelete('cascade');
            $table->string('description')->nullable();
            $table->string('part_number')->nullable();
            $table->integer('quantity')->nullable();
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
        Schema::dropIfExists('job_maintenances');
        Schema::dropIfExists('job_replacement_parts');
    }
};
