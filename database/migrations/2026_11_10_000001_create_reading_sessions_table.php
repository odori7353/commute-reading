<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 読書の記録テーブルを作る
     */
    public function up(): void
    {
        Schema::create('reading_sessions', function (Blueprint $table) {
            $table->id();
            // 誰の記録か。ユーザーを削除したら記録も一緒に消す
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // 開始時刻
            $table->dateTime('started_at');
            // 終了時刻。null のあいだは「読書中」
            $table->dateTime('ended_at')->nullable();
            // 読書時間（秒）。終了時に保存する
            $table->unsignedInteger('duration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * 元に戻すときはテーブルを消す
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_sessions');
    }
};
