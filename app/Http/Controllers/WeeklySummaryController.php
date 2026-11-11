<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class WeeklySummaryController extends Controller
{
    /**
     * 今週（月曜〜日曜）の読書時間の合計と、曜日ごとの内訳を表示する
     */
    public function index(Request $request): View
    {
        // 今週の月曜 0:00 と日曜 23:59:59（タイムゾーンは Asia/Tokyo）
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // 今週開始した、終了済みの記録だけを取り出す
        // 読書中（ended_at が null）の行は、まだ duration がないので含めない
        $sessions = $request->user()
            ->readingSessions()
            ->whereNotNull('ended_at')
            ->whereBetween('started_at', [$weekStart, $weekEnd])
            ->get();

        // 合計（秒）。duration を足すだけ
        $totalSeconds = $sessions->sum('duration');

        // 曜日ごとの合計（秒）。月曜=1 〜 日曜=7 の7個の箱を 0 で用意してから足していく
        $labels = [1 => '月', 2 => '火', 3 => '水', 4 => '木', 5 => '金', 6 => '土', 7 => '日'];
        $daily = array_fill_keys(array_keys($labels), 0);

        foreach ($sessions as $session) {
            // 日をまたいだ記録は、開始した日の分として数える
            $daily[$session->started_at->dayOfWeekIso] += $session->duration;
        }

        // グラフの棒の高さを決めるための一番大きい値（0 で割らないよう最低 1）
        $maxSeconds = max(1, max($daily));

        return view('weekly', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'totalSeconds' => $totalSeconds,
            'labels' => $labels,
            'daily' => $daily,
            'maxSeconds' => $maxSeconds,
            'today' => now()->dayOfWeekIso,
        ]);
    }

    /**
     * 秒数を「2h 15m」の形にする（画面から呼ぶ）
     */
    public static function format(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        return $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";
    }
}
