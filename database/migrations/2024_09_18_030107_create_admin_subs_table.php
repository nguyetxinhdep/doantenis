<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminSubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_sub', function (Blueprint $table) {
            $table->id('Admin_sub_id');
            $table->integer('Admin_sub_code')->nullable();
            $table->foreignId('user_id')->constrained('users', 'User_id')->onDelete('cascade'); // Liên kết với bảng users
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
        Schema::dropIfExists('admin_sub');
    }
}
