<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTypesTable extends Migration
{
    public function up()
    {
        Schema::create('customer_types', function (Blueprint $table) {
            $table->id('Customer_type_id');
            $table->string('Name', 10);
            $table->string('Description', 225)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_types');
    }
}
