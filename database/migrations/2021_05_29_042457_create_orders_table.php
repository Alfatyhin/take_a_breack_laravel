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
            $table->string('order_id')->unique()->index();
            $table->string('order_status')->default('AWAITING_PROCESSING');
            $table->string('amoId')->nullable()->index();
            $table->string('amoStatus')->nullable();
            $table->string('gclientId')->nullable();
            $table->integer('clientId')->nullable()->index();
            $table->smallInteger('paymentMethod')->default(0);
            $table->smallInteger('paymentStatus')->default(0);
            $table->dateTime('paymentDate')->nullable();
            $table->boolean('invoiceStatus')->default(false);
            $table->json('invoiceData')->nullable();
            $table->float('orderPrice')->default(0);
            $table->json('orderData')->nullable();
            $table->json('amoData')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
