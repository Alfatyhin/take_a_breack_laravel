<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id();
            $table->string('sid');
            $table->string('gid')->nullable();
            $table->string('order_id')->nullable();
            $table->string('url');
            $table->string('get_param')->nullable();
            $table->string('ip');
            $table->text('note')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('event')->nullable();
            $table->json('post_data')->nullable();
            $table->string('utm_id')->nullable();
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
        Schema::dropIfExists('statistics');
    }
}
