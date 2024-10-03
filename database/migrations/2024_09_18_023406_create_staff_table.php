<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id('Staff_id');
            $table->integer('Staff_code')->nullable();
            $table->foreignId('user_id')->constrained('users', 'User_id')->onDelete('cascade'); // Liên kết với bảng users
            $table->foreignId('branch_id')->constrained('branches', 'Branch_id')->onDelete('cascade');
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
        Schema::dropIfExists('staff');
    }
}
