<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeSlotsTable extends Migration
{
    public function up()
    {
        Schema::create('time_slots', function (Blueprint $table) {
            $table->id('Time_slot_id');
            $table->time('Start_time');
            $table->time('End_time');
            $table->boolean('Status');
            // $table->foreignId('court_id')->constrained('courts', 'Court_id')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches', 'Branch_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('time_slots');
    }
}
