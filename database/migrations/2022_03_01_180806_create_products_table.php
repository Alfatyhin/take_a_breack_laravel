<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->bigInteger('index_num')->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->string('sku')->unique();
            $table->boolean('enabled')->default(false);
            $table->boolean('unlimited')->default(false);
            $table->integer('count')->default(0);
            $table->float('price')->default(0);
            $table->float('compareToPrice')->default(0);
            $table->json('variables')->nullable();
            $table->json('options')->nullable();
            $table->json('discount')->nullable();
            $table->json('image')->nullable();
            $table->json('galery')->nullable();
            $table->text('desc')->nullable();
            $table->longText('description')->nullable();
            $table->json('translate')->nullable();
            $table->json('categories')->nullable();
            $table->json('data')->nullable();
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
        Schema::dropIfExists('products');
    }
}
