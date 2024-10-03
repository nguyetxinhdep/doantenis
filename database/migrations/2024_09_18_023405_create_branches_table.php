<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id('Branch_id');
            $table->string('Name', 100);
            $table->string('Location', 225);
            $table->integer('Phone', false, true)->length(12);
            $table->string('Email', 70)->nullable();
            $table->foreignId('manager_id')->constrained('managers', 'Manager_id')->onDelete('cascade');
            $table->integer('Status')->nullable();
            $table->string('Image', 255)->nullable();
            $table->string('Cover_image', 255)->nullable();
            $table->text('link_map')->nullable();
            $table->timestamps();
            // $table->foreignId('staff_id')->constrained('staff','Staff_id')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
