<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mold_id')->comment('金型ID');
            $table->foreign('mold_id')->references('id')->on('molds')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->comment('使用者ID');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->dateTime('start_time')->comment('使用開始日時');
            $table->dateTime('end_time')->nullable()->comment('使用終了日時');
            $table->unsignedInteger('duration_minutes')->nullable()->comment('使用時間（分）');
            $table->timestamps();
            // 金型・ユーザー・日付別に絞り込み用
            $table->index('mold_id');
            $table->index('user_id');
            $table->index('start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_logs');
    }
};

/**
 * 将来の拡張メモ（v2.0）：
 *   - 使用履歴をCSV出力機能追加時にこのテーブルをベースにエクスポート処理を実装
 *   - 月次・年次の稼働率レポート生成の集計元テーブルとしても使用できる？
 *   - 現在は持ち出し時間（start_time〜end_time）のみ管理
 *   - 設備側のPLCやIoTセンサーからAPIで取得できればサイクルタイム・ショット数・使用設備IDなどもこのテーブルに追加して管理できる？
 */