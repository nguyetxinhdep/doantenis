<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceListsTable extends Migration
{
    public function up()
    {
        Schema::create('price_list', function (Blueprint $table) {
            $table->id('Price_list_id');
            $table->float('Price');
            // $table->foreignId('court_id')->constrained('courts','Court_id')->onDelete('cascade');
            $table->foreignId('time_slot_id')->constrained('time_slots', 'Time_slot_id')->onDelete('cascade');
            $table->foreignId('customer_type_id')->constrained('customer_types', 'Customer_type_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('price_list');
    }
}
