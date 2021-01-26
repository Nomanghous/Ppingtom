<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToSubCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('product_sub_category', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id', 'category_fk_3040759')->references('id')->on('product_categories');
        });
    }
}
