<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 乗車時間の設定テーブルを作る
     */
    public function up(): void
    {
        Schema::create('commute_settings', function (Blueprint $table) {
            $table->id();
            // 誰の設定か。ユーザーを削除したら設定も一緒に消す
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // 'going'（行き）か 'return'（帰り）
            $table->string('direction');
            // トグルのオン/オフ
            $table->boolean('enabled')->default(false);
            // 乗車時刻
            $table->time('departs_at');
            // 曜日ごとのオン/オフ
            $table->boolean('monday')->default(false);
            $table->boolean('tuesday')->default(false);
            $table->boolean('wednesday')->default(false);
            $table->boolean('thursday')->default(false);
            $table->boolean('friday')->default(false);
            $table->boolean('saturday')->default(false);
            $table->boolean('sunday')->default(false);
            $table->timestamps();

            // 同じ人の「行き」が2行できないようにする
            $table->unique(['user_id', 'direction']);
        });
    }

    /**
     * 元に戻すときはテーブルを消す
     */
    public function down(): void
    {
        Schema::dropIfExists('commute_settings');
    }
};
