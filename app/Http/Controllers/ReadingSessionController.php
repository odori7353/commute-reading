<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReadingSessionController extends Controller
{
    /**
     * 記録画面を表示する
     * ended_at が null の行があれば「読書中」として画面に渡す
     */
    public function index(Request $request): View
    {
        $current = $request->user()
            ->readingSessions()
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        return view('record', ['current' => $current]);
    }

    /**
     * 読書を開始する：開始時刻だけ入れて1行作る
     */
    public function start(Request $request): RedirectResponse
    {
        $user = $request->user();

        // すでに読書中なら、新しい行は作らない（ボタンの二度押し対策）
        if ($user->readingSessions()->whereNull('ended_at')->exists()) {
            return redirect()->route('record');
        }

        // リレーション経由で作るので、user_id は自動でログイン中のユーザーになる
        $user->readingSessions()->create([
            'started_at' => now(),
        ]);

        return redirect()->route('record');
    }

    /**
     * 読書を終了する：終了時刻と読書時間（秒）を保存する
     */
    public function finish(Request $request): RedirectResponse
    {
        $current = $request->user()
            ->readingSessions()
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        // 読書中の記録がなければ何もしない
        if ($current === null) {
            return redirect()->route('record');
        }

        $endedAt = now();

        // Carbon 3 の diffInSeconds は小数を返し、順番を逆にするとマイナスになる
        // 「開始->diffInSeconds(終了)」の順で計算し、整数にして保存する
        $duration = (int) $current->started_at->diffInSeconds($endedAt);

        $current->update([
            'ended_at' => $endedAt,
            'duration' => $duration,
        ]);

        $minutes = intdiv($duration, 60);

        return redirect()
            ->route('record')
            ->with('status', "おつかれさまでした。{$minutes}分読みました。");
    }
}
