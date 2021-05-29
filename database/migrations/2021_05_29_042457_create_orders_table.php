<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('ecwidId')->unique();
            $table->string('ecwidStatus')->default('AWAITING_PROCESSING');
            $table->string('amoId')->nullable();
            $table->string('amoStatus')->nullable();
            $table->smallInteger('paymentMethod')->default(0);
            $table->smallInteger('paymentStatus')->default(0);
            $table->boolean('invoiceStatus')->default(false);
            $table->float('orderPrice');
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
        Schema::dropIfExists('orders');
    }
}
