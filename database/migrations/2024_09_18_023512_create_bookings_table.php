<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('Booking_id');
            $table->boolean('Status');
            $table->timestamps();
            $table->date('Date_booking');
            $table->time('Start_time');
            $table->time('End_time');
            $table->foreignId('court_id')->constrained('courts', 'Court_id')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('time_slots', 'Time_slot_id')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers', 'Customer_id')->onDelete('cascade');
            $table->foreignId('price_list_id')->constrained('price_list', 'Price_list_id')->onDelete('cascade');
            $table->foreignId('branch_id')->constrained('branches', 'Branch_id')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
