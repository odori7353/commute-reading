<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommuteSetting extends Model
{
    /**
     * 行き・帰りの種類と、画面に出す名前
     */
    public const DIRECTIONS = [
        'going' => '行き',
        'return' => '帰り',
    ];

    /**
     * 曜日のカラム名と、画面に出す名前（月曜はじまり）
     */
    public const DAYS = [
        'monday' => '月',
        'tuesday' => '火',
        'wednesday' => '水',
        'thursday' => '木',
        'friday' => '金',
        'saturday' => '土',
        'sunday' => '日',
    ];

    /**
     * create() や update() でまとめて代入してよいカラム
     * user_id はリレーション経由で入れるので、ここには含めない
     *
     * @var list<string>
     */
    protected $fillable = [
        'direction',
        'enabled',
        'departs_at',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
        'sunday',
    ];

    /**
     * 取り出すときの型（DBでは 0/1 で入っている boolean を true/false にする）
     */
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'monday' => 'boolean',
            'tuesday' => 'boolean',
            'wednesday' => 'boolean',
            'thursday' => 'boolean',
            'friday' => 'boolean',
            'saturday' => 'boolean',
            'sunday' => 'boolean',
        ];
    }

    /**
     * この設定の持ち主
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * まだ保存していないときに画面へ出す初期値（DBには保存しない）
     */
    public static function defaultFor(string $direction): self
    {
        return new self([
            'direction' => $direction,
            'enabled' => false,
            'departs_at' => $direction === 'going' ? '08:00' : '18:00',
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => false,
            'sunday' => false,
        ]);
    }

    /**
     * 入力欄に出すための「08:10」形式の時刻（DBには 08:10:00 で入っている）
     */
    public function departsAtForInput(): string
    {
        return substr((string) $this->departs_at, 0, 5);
    }

    /**
     * $now より後で、いちばん近い乗車日時を返す（なければ null）
     * 今日から7日先まで1日ずつ見て、乗る曜日なら「その日の乗車時刻」を候補にする
     */
    public function nextDepartureAfter(CarbonInterface $now): ?CarbonInterface
    {
        if (! $this->enabled) {
            return null;
        }

        [$hour, $minute] = array_map('intval', explode(':', $this->departsAtForInput()));
        $dayColumns = array_keys(self::DAYS);

        for ($offset = 0; $offset <= 7; $offset++) {
            $candidate = $now->copy()->addDays($offset)->setTime($hour, $minute);
            // dayOfWeekIso は 月=1〜日=7 なので、-1 して DAYS の並びに合わせる
            $column = $dayColumns[$candidate->dayOfWeekIso - 1];

            if ($this->{$column} && $candidate->greaterThan($now)) {
                return $candidate;
            }
        }

        return null;
    }
}
