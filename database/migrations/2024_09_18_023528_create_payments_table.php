<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id('Payment_id');
            $table->double('Amount');
            $table->string('Payment_method', 10);
            $table->dateTime('Payment_date');
            $table->double('Debt')->nullable();
            $table->double('Paid');
            $table->boolean('Status');
            $table->foreignId('branch_id')->constrained('branches', 'Branch_id')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('bookings', 'Booking_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
