<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssueCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issue_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('issue_id')->comment('課題ID');
            $table->unsignedBigInteger('category_id')->comment('カテゴリーID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('issue_categories');
    }
}
