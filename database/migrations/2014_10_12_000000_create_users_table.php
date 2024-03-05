<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\RoleType;
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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('password_change_date')->nullable();
            $table->string('first_name', 150)->nullable();
            $table->string('last_name', 150)->nullable();
            $table->string('position', 150)->nullable();
            $table->string('last_ip_address', 255)->nullable();
            $table->dateTime('last_activity')->nullable();
            $table->integer('incorrect_logins')->nullable();
            $table->boolean('is_active')->default(true)->nullable();
            $table->string('google_id')->nullable();
            $table->tinyInteger('role')->default(RoleType::USER->value);
            $table->string('avatar')->nullable();
            $table->integer('created_by')->default(0)->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('sitehead_user_id')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
