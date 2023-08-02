<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKindsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kinds', function (Blueprint $table) {
            $table->bigIncrements('kind_id')->comment('種別ID');
            $table->string('kind_name')->unique()->comment('種別名');
            $table->string('kind_desc')->comment('種別内容');
            $table->string('hex_color', 7)->unique()->comment('カラーコード');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kinds');
    }
}
