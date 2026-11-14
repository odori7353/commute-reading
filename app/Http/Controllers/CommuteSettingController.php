<?php

namespace App\Http\Controllers;

use App\Models\CommuteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommuteSettingController extends Controller
{
    /**
     * 乗車時間の設定画面を表示する
     * まだ保存していなければ、初期値（行き 8:00 / 帰り 18:00、平日）を入れて表示する
     */
    public function edit(Request $request): View
    {
        $saved = $request->user()->commuteSettings()->get()->keyBy('direction');

        $settings = [];
        foreach (CommuteSetting::DIRECTIONS as $direction => $label) {
            $settings[$direction] = $saved[$direction] ?? CommuteSetting::defaultFor($direction);
        }

        return view('commute-settings', [
            'settings' => $settings,
            'directions' => CommuteSetting::DIRECTIONS,
            'days' => CommuteSetting::DAYS,
        ]);
    }

    /**
     * 行き・帰りの設定をまとめて保存する
     */
    public function update(Request $request): RedirectResponse
    {
        // 時刻は「08:10」の形だけ受け付ける
        $request->validate([
            'settings.going.departs_at' => ['required', 'date_format:H:i'],
            'settings.return.departs_at' => ['required', 'date_format:H:i'],
        ], [
            'settings.*.departs_at.required' => '時刻を入力してください。',
            'settings.*.departs_at.date_format' => '時刻は 08:10 の形で入力してください。',
        ]);

        foreach (array_keys(CommuteSetting::DIRECTIONS) as $direction) {
            $key = "settings.{$direction}";

            // チェックボックスは、オフのときは何も送られてこない
            // $request->boolean() なら「送られてこない = false」として扱える
            $data = [
                'enabled' => $request->boolean("{$key}.enabled"),
                'departs_at' => $request->input("{$key}.departs_at"),
            ];
            foreach (array_keys(CommuteSetting::DAYS) as $day) {
                $data[$day] = $request->boolean("{$key}.{$day}");
            }

            // 1行目のキーで探して、あれば更新・なければ作成する
            // リレーション経由なので user_id は自動でログイン中のユーザーになる
            $request->user()->commuteSettings()->updateOrCreate(
                ['direction' => $direction],
                $data,
            );
        }

        return redirect()
            ->route('commute-settings.edit')
            ->with('status', '保存しました。');
    }
}
