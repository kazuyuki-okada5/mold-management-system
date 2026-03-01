<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * TODO: 保管場所マスタ（優先度: 高）
 * 実装予定: v2.0
 * 概要: 倉庫・フロア・棚番号を正規化したマスタテーブル
 *       現在はmoldsテーブルに warehouse / floor / shelf_number を直接持たせているが、
 *       在庫管理機能追加時にこのテーブルへ移行する
 * 依存: なし（親テーブル）
 */

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 実装時にコメントアウトを解除する
        // Schema::create('locations', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('warehouse', 50)->comment('倉庫名 例: A棟');
        //     $table->string('floor', 50)->comment('フロア 例: 1F');
        //     $table->string('shelf_number', 50)->comment('棚番号 例: A-01');
        //     $table->string('description')->nullable()->comment('備考');
        //     $table->timestamps();
        //
        //     // 同じ倉庫・フロア・棚番号の組み合わせは一意
        //     $table->unique(['warehouse', 'floor', 'shelf_number']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('locations');
    }
};
