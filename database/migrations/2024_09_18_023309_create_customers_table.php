<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('Customer_id');
            $table->string('Customer_code', 20)->nullable();
            $table->timestamps();
            $table->foreignId('user_id')->constrained('users', "User_id")->onDelete('cascade');
            // $table->foreignId('customer_type_id')->constrained('customer_types', 'Customer_type_id')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
}
