{{-- 秒数を「2h 15m」にする関数を Summary という名前で使えるようにする --}}
@use('App\Http\Controllers\WeeklySummaryController', 'Summary')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            今週の合計
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto px-4">
            <div class="bg-white shadow-sm rounded-lg p-8">

                {{-- 期間と合計時間 --}}
                <p class="text-center text-gray-500">
                    {{ $weekStart->format('n/j') }}（月）〜 {{ $weekEnd->format('n/j') }}（日）
                </p>
                <p class="my-6 text-center text-6xl font-bold tabular-nums text-gray-800">
                    {{ Summary::format($totalSeconds) }}
                </p>

                {{-- 曜日ごとの棒グラフ（ライブラリを使わず、高さを % で指定） --}}
                <div class="flex items-end justify-between gap-2 h-40 border-b border-gray-200">
                    @foreach ($labels as $day => $label)
                        <div class="flex-1 h-full flex flex-col justify-end items-center">
                            @if ($daily[$day] > 0)
                                <span class="mb-1 text-xs text-gray-500">{{ intdiv($daily[$day], 60) }}m</span>
                            @endif
                            <div class="w-full rounded-t {{ $day === $today ? 'bg-indigo-600' : 'bg-indigo-300' }}"
                                 style="height: {{ round($daily[$day] / $maxSeconds * 100) }}%"></div>
                        </div>
                    @endforeach
                </div>

                {{-- 曜日のラベル（今日を太字に） --}}
                <div class="flex justify-between gap-2 mt-2">
                    @foreach ($labels as $day => $label)
                        <span class="flex-1 text-center text-sm {{ $day === $today ? 'font-bold text-gray-800' : 'text-gray-500' }}">
                            {{ $label }}
                        </span>
                    @endforeach
                </div>

                <a href="{{ route('record') }}"
                   class="block mt-8 text-center text-indigo-600 underline">
                    記録画面に戻る
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
