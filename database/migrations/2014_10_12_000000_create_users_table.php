<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('User_id');
            $table->string('Name', 30);
            $table->string('Email', 70)->nullable();
            $table->integer('Phone', false, true)->length(12);
            $table->string('Address', 225)->nullable();
            $table->string('Role', 10);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('token_change_pass', 225)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
