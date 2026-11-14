<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            読書の記録
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto px-4">
            <div class="bg-white shadow-sm rounded-lg p-8 text-center">

                {{-- 次の乗車（乗車時間設定から計算） --}}
                @if ($nextDeparture)
                    <p class="mb-4 text-sm text-gray-500">
                        次の乗車は
                        <span class="font-bold text-gray-800">
                            @if ($nextDeparture->isToday())
                                {{ $nextDeparture->format('G:i') }}
                            @elseif ($nextDeparture->isTomorrow())
                                明日 {{ $nextDeparture->format('G:i') }}
                            @else
                                {{ $nextDeparture->format('n/j') }}（{{ ['月','火','水','木','金','土','日'][$nextDeparture->dayOfWeekIso - 1] }}）{{ $nextDeparture->format('G:i') }}
                            @endif
                        </span>
                    </p>
                @else
                    <p class="mb-4 text-sm text-gray-500">
                        <a href="{{ route('commute-settings.edit') }}" class="underline">乗車時間を設定する</a>
                    </p>
                @endif

                {{-- 終了したときのメッセージ --}}
                @if (session('status'))
                    <p class="mb-6 text-green-700">{{ session('status') }}</p>
                @endif

                @if ($current)
                    {{-- 読書中：経過時間を表示して、終了ボタンを出す --}}
                    <p class="text-gray-500">読書中</p>
                    <p id="elapsed"
                       class="my-6 text-6xl font-bold tabular-nums text-gray-800"
                       data-started-at="{{ $current->started_at->getTimestampMs() }}">
                        00:00
                    </p>

                    <form method="POST" action="{{ route('record.finish') }}">
                        @csrf
                        <button type="submit"
                                class="w-full py-6 rounded-full bg-gray-800 text-white text-2xl font-bold">
                            終了
                        </button>
                    </form>
                @else
                    {{-- 読書していない：開始ボタンを出す --}}
                    <p class="text-gray-500">カバンから本を出そう</p>
                    <p class="my-6 text-6xl font-bold tabular-nums text-gray-300">00:00</p>

                    <form method="POST" action="{{ route('record.start') }}">
                        @csrf
                        <button type="submit"
                                class="w-full py-6 rounded-full bg-indigo-600 text-white text-2xl font-bold">
                            開始
                        </button>
                    </form>
                @endif

            </div>

            <a href="{{ route('weekly') }}"
               class="block mt-6 text-center text-indigo-600 underline">
                今週の合計を見る
            </a>
        </div>
    </div>

    @if ($current)
        <script>
            // 開始時刻（DBに保存したもの）から、今までの経過時間を1秒ごとに表示する
            // 画面を閉じても開始時刻はDBにあるので、開き直すと続きから表示される
            const elapsed = document.getElementById('elapsed');
            const startedAt = Number(elapsed.dataset.startedAt);

            const pad = (n) => String(n).padStart(2, '0');

            const render = () => {
                const seconds = Math.max(0, Math.floor((Date.now() - startedAt) / 1000));
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = seconds % 60;
                elapsed.textContent = h > 0 ? `${h}:${pad(m)}:${pad(s)}` : `${pad(m)}:${pad(s)}`;
            };

            render();
            setInterval(render, 1000);
        </script>
    @endif
</x-app-layout>
