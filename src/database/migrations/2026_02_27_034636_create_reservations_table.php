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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            // カスケードで予約も削除出来る？動作確認時に確認する。
            $table->unsignedBigInteger('mold_id')->comment('金型ID');
            $table->foreign('mold_id')->references('id')->on('molds')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->comment('予約者ID');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('approved_by')->nullable()->comment('承認者ID');
            // nullOnDelete()＝承認者を削除しても予約レコードを残す為。
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            // 重複チェック用。
            $table->dateTime('reserved_start')->comment('予約開始日時');
            $table->dateTime('reserved_end')->comment('予約終了日時');
            /**
             * ステータス管理（状態遷移）
             *   ライフサイクル制御（enum使用）
             *      pending（承認待ち）   → approved または rejectedを管理者が判断
             *      approved（承認済み）  → cancelled または completedを作業者が判断
             *      rejected（否認）  → （終端）
             *      cancelled → （終端）
             *      completed → （終端）
             */
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled', 'completed'])->default('pending')->comment('状態');
            $table->text('purpose')->nullable()->comment('使用目的');
            $table->text('reject_reason')->nullable()->comment('否認理由');
            $table->timestamp('approved_at')->nullable()->comment('承認日時');
            $table->timestamps();
            $table->index(['mold_id', 'reserved_start', 'reserved_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};

/**
 * 将来の拡張メモ（v2.0）：
 *   - メール通知機能追加する場合は notified_at のようなカラムを追加
 *   - 繰り返し予約（定期予約）機能の追加も出来るのか検討
 */