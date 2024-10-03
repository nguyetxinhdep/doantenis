<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManagersTable extends Migration
{
    public function up()
    {
        Schema::create('managers', function (Blueprint $table) {
            $table->id('Manager_id');
            $table->integer('Manager_code')->nullable();
            $table->foreignId('user_id')->constrained('users', 'User_id')->onDelete('cascade');
            // $table->foreignId('branch_id')->constrained('branches', 'Branch_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('managers');
    }
}
