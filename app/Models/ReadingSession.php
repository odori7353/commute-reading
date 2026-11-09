<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingSession extends Model
{
    /**
     * create() や update() でまとめて代入してよいカラム
     * user_id はリレーション経由で入れるので、ここには含めない
     *
     * @var list<string>
     */
    protected $fillable = [
        'started_at',
        'ended_at',
        'duration',
    ];

    /**
     * 取り出すときの型
     */
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'duration' => 'integer',
        ];
    }

    /**
     * この記録の持ち主
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
