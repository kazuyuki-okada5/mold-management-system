<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TODO: 入出庫履歴テーブル（優先度: 中）
 * 実装予定: v2.0
 * 概要: 金型の入庫・出庫・移動を記録するトランザクションログ
 *       在庫数の増減はこのテーブルへの記録と同時に mold_stocks.quantity を更新する
 * 依存: locations・mold_stocks テーブルを先に実装すること
 *       実装順序: locations → mold_stocks → stock_movements
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 実装時にコメントアウトを解除する
        // Schema::create('stock_movements', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('mold_id')
        //           ->constrained()
        //           ->cascadeOnDelete()
        //           ->comment('対象金型ID');
        //     $table->foreignId('user_id')
        //           ->constrained()
        //           ->cascadeOnDelete()
        //           ->comment('操作者ID');
        //     $table->foreignId('from_location_id')
        //           ->nullable()
        //           ->constrained('locations')
        //           ->nullOnDelete()
        //           ->comment('移動元保管場所（入庫の場合はNULL）');
        //     $table->foreignId('to_location_id')
        //           ->nullable()
        //           ->constrained('locations')
        //           ->nullOnDelete()
        //           ->comment('移動先保管場所（出庫の場合はNULL）');
        //     $table->enum('movement_type', ['in', 'out', 'transfer'])
        //           ->comment('in=入庫 / out=出庫 / transfer=場所移動');
        //     $table->unsignedInteger('quantity')->default(1)->comment('数量');
        //     $table->text('note')->nullable()->comment('備考・理由');
        //     $table->timestamp('moved_at')->useCurrent()->comment('移動日時');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('mold_stocks');
    }
};
