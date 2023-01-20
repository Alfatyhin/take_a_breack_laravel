<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->text('name')->nullable();
            $table->string('slag')->unique();
            $table->bigInteger('index_num')->nullable();
            $table->boolean('enabled')->default(true);
            $table->json('image')->nullable();
            $table->longText('description')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->json('translate')->nullable();
            $table->json('products')->nullable();
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
        Schema::dropIfExists('categories');
    }
}
