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
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->float('latitdue', 12, 10)->nullable();
            $table->float('longitude', 12, 10)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
