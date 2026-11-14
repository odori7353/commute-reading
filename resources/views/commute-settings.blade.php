<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            乗車時間設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto px-4">
            <form method="POST" action="{{ route('commute-settings.update') }}"
                  class="bg-white shadow-sm rounded-lg p-8 space-y-8">
                @csrf
                @method('PUT')

                @if (session('status'))
                    <p class="text-center text-green-700">{{ session('status') }}</p>
                @endif

                {{-- old() は、保存に失敗して戻ってきたときの入力値。あればそちらを優先して表示する --}}
                @foreach ($directions as $direction => $label)
                    @php $setting = $settings[$direction]; @endphp

                    <section class="space-y-4">
                        {{-- 見出しとトグル（中身はチェックボックス） --}}
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-gray-800">{{ $label }}</h3>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[{{ $direction }}][enabled]" value="1"
                                       class="sr-only peer"
                                       @checked(old() ? old("settings.$direction.enabled") : $setting->enabled)>
                                <div class="w-12 h-7 bg-gray-300 rounded-full peer-checked:bg-indigo-600
                                            after:content-[''] after:absolute after:top-1 after:left-1
                                            after:w-5 after:h-5 after:bg-white after:rounded-full
                                            after:transition-all peer-checked:after:translate-x-5"></div>
                            </label>
                        </div>

                        {{-- 乗車時刻 --}}
                        <div>
                            <input type="time" name="settings[{{ $direction }}][departs_at]"
                                   value="{{ old("settings.$direction.departs_at", $setting->departsAtForInput()) }}"
                                   class="w-full rounded-md border-gray-300 text-2xl tabular-nums">
                            <x-input-error :messages="$errors->get('settings.' . $direction . '.departs_at')" class="mt-2" />
                        </div>

                        {{-- 曜日（押すと色が変わるボタン。中身はチェックボックス） --}}
                        <div class="flex justify-between gap-1">
                            @foreach ($days as $day => $dayLabel)
                                <label class="flex-1">
                                    <input type="checkbox" name="settings[{{ $direction }}][{{ $day }}]" value="1"
                                           class="sr-only peer"
                                           @checked(old() ? old("settings.$direction.$day") : $setting->{$day})>
                                    <span class="block py-2 text-center rounded-md border border-gray-300 text-gray-500 cursor-pointer
                                                 peer-checked:bg-indigo-600 peer-checked:border-indigo-600 peer-checked:text-white">
                                        {{ $dayLabel }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </section>
                @endforeach

                <x-primary-button class="w-full justify-center py-3 text-base">
                    保存
                </x-primary-button>

                <p class="text-xs text-gray-500">
                    設定した時刻は、記録画面の「次の乗車」の表示に使います。
                    今後、この時刻にプッシュ通知（Web Push）を送る予定です。
                </p>
            </form>
        </div>
    </div>
</x-app-layout>
