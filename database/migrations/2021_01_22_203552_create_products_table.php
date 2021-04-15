<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->timestamp('news_date')->useCurrent();
            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('location_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
