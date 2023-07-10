<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('issues', function (Blueprint $table) {
            $table->bigIncrements('issue_id')->comment('課題ID');
            $table->unsignedBigInteger('project_id')->comment('プロジェクトID');
            $table->string('issue_cd', 20)->unique()->comment('課題コード(プロジェクトコード + \'_\' + 連番)');
            $table->string('issue_title')->comment('課題タイトル');
            $table->longText('issue_desc')->comment('課題内容');
            $table->enum('issue_priority', ['high', 'medium', 'low'])->comment('優先度 high:高 medium:中 low:低');
            $table->unsignedBigInteger('version_id')->comment('バージョンID');
            $table->tinyInteger('estimated_time')->nullable()->comment('予定時間');
            $table->tinyInteger('completion_time')->nullable()->comment('実施時間');
            $table->enum('complete_reason', ['完了', '未再現', '対応しない'])->nullable()->comment('完了理由 完了 未再現 対応しない');
            $table->unsignedBigInteger('assignee_id')->comment('担当者ID');
            $table->unsignedBigInteger('milestone_id')->comment('マイルストーンID');
            $table->dateTime('start_date')->comment('開始日');
            $table->dateTime('end_date')->comment('終了日');
            $table->unsignedBigInteger('status_id')->comment('状態ID');
            $table->unsignedBigInteger('kind_id')->comment('種別ID');
            $table->enum('issue_rank', ['parent', 'child', 'grandchild'])->comment('課題ランク 親:parent 子:child 孫:grandchild');
            $table->unsignedBigInteger('parent_issue_id')->nullable()->comment('親課題ID');
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
        Schema::dropIfExists('issues');
    }
}
