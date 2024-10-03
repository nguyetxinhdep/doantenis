<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourtsTable extends Migration
{
    public function up()
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->id('Court_id');
            $table->string('Name', 225);
            $table->boolean('Availability');
            $table->timestamps();
            $table->foreignId('branch_id')->constrained('branches','Branch_id')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('courts');
    }
}
