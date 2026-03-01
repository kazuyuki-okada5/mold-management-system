<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TODO: 金型在庫テーブル（優先度: 高）
 * 実装予定: v2.0
 * 概要: 金型と保管場所の紐付け・現在の在庫数を管理する中間テーブル
 *       1つの金型が複数の保管場所に分散している場合も対応できる設計
 * 依存: locations テーブルを先に実装すること
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 実装時にコメントアウトを解除する
        // Schema::create('mold_stocks', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('mold_id')
        //           ->constrained()
        //           ->cascadeOnDelete()
        //           ->comment('金型ID');
        //     $table->foreignId('location_id')
        //           ->constrained()
        //           ->cascadeOnDelete()
        //           ->comment('保管場所ID');
        //     $table->unsignedInteger('quantity')->default(1)->comment('在庫数');
        //     $table->timestamps();
        //
        //     // 同じ金型が同じ場所に重複登録されないように
        //     $table->unique(['mold_id', 'location_id']);
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
