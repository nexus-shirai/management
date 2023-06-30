<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('user_id')->comment('ユーザーID');
            $table->string('username')->comment('ユーザー名');
            $table->string('first_name')->comment('名');
            $table->string('last_name')->comment('姓');
            $table->string('first_name_kana')->comment('メイ');
            $table->string('last_name_kana')->comment('セイ');
            $table->string('email')->unique()->comment('メールアドレス');
            $table->string('password')->comment('パスワード');
            $table->enum('user_type', [User::USER_TYPE_ADMIN, User::USER_TYPE_GENERAL])
                ->comment('ユーザー種別 admin:管理者 general:一般ユーザー');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
