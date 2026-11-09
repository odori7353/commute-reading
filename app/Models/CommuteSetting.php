<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommuteSetting extends Model
{
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
}
