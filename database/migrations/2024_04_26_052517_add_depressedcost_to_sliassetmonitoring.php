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
        Schema::table('sliassetmonitoring', function (Blueprint $table) {
            $table->decimal('depreciationcost',10, 2)->nullable()->after('purchasecost');
            $table->boolean('is_active')->nullable()->default(1)->after('depreciationcost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sliassetmonitoring', function (Blueprint $table) {
            $table->dropColumn('depreciationcost');
            $table->dropColumn('is_active');
        });
    }
};
