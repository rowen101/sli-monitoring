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
        Schema::create('sliassetmonitoring', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained();
            $table->string('asset_name')->nullable(false);
            $table->string('asset_type')->nullable(false);
            $table->string('serial')->nullable();
            $table->date('date_acquired')->nullable();
            $table->string('man_supplier')->nullable();
            $table->string('unit')->nullable();
            $table->string('location')->nullable();
            $table->string('paccountable')->nullable();
            $table->integer('locationchangetranfer')->nullable();
            $table->string('cucodition')->nullable();
            $table->text('maintenancenotes')->nullable();
            $table->date('lastmaintenance')->nullable();
            $table->date('nextmaintenance')->nullable();
            $table->integer('operationhours')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('purchasecost', 10, 2)->nullable();
            $table->text('insurancewarrantyinfo')->nullable();
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
        //
    }
};
