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
        Schema::create('molds', function (Blueprint $table) {
            $table->id();
            $table->string('mold_number', 50)->unique()->comment('金型番号');
            $table->string('name')->comment('金型名');
            $table->text('specifications')->nullable()->comment('仕様');
            $table->date('manufacture_date')->comment('製造日');
            $table->enum('status', ['待機中', '使用中', '予約済み', 'メンテナンス中'])->default('待機中')->comment('状態');
            $table->string('warehouse', 50)->nullable()->comment('倉庫');
            $table->string('floor', 50)->nullable()->comment('フロア');
            $table->string('shelf_number', 50)->nullable()->comment('棚番号');
            $table->unsignedInteger('total_usage_count')->default(0)->comment('累計使用回数');
            $table->unsignedInteger('max_usage_count')->nullable()->comment('最大使用回数（寿命）');
            $table->softDeletes();
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('molds');
    }
};

/**
 * 将来の拡張メモ（v2.0）：
 *   - 同じ型番の金型を複数個持つ場合は mold_stocks テーブルでどの場所に何個あるか」を管理する
 *   - 今回は実装が早くできるように金型テーブルに場所を直書きしているが別テーブル（locationsテーブル）で管理にすると容量管理まで出来る。（既存カラムのwarehouse/floor/shelf_numberは削除する）
 */