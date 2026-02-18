@php
    $records = $this->getRecords();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📖 Progres Hafalan Terbaru Santri Binaan
        </x-slot>

        @if($records->isEmpty())
            <div class="flex flex-col items-center justify-center py-6 text-center">
                <x-heroicon-o-book-open class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" />
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada setoran hafalan</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Santri</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Surah</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Juz</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Ayat</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Nilai</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Status</th>
                            <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($records as $record)
                            @php
                                $nilaiColor = match($record->nilai_avg) {
                                    'A' => 'success',
                                    'B' => 'info',
                                    'C' => 'warning',
                                    'D' => 'danger',
                                    default => 'gray',
                                };
                                $nilaiIcon = match($record->nilai_avg) {
                                    'A' => '⭐',
                                    'B' => '✅',
                                    'C' => '⚠️',
                                    'D' => '❌',
                                    default => '➖',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-m-user class="w-4 h-4 text-gray-400" />
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $record->student?->student_name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-m-book-open class="w-4 h-4 text-gray-400" />
                                        <span class="text-gray-700 dark:text-gray-300">{{ $record->surah?->surah_name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <x-filament::badge color="primary" size="sm">
                                        {{ $record->juz ?? '-' }}
                                    </x-filament::badge>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">
                                    {{ $record->from ?? '-' }} — {{ $record->to ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <x-filament::badge :color="$nilaiColor" size="sm">
                                        {{ $nilaiIcon }} {{ $record->nilai_avg ?? '-' }}
                                    </x-filament::badge>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($record->complete == 1)
                                        <x-filament::badge color="success" size="sm">Selesai</x-filament::badge>
                                    @else
                                        <x-filament::badge color="warning" size="sm">Belum</x-filament::badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-xs text-gray-500 dark:text-gray-400" title="{{ $record->created_at?->translatedFormat('d F Y, H:i') }}">
                                        {{ $record->created_at?->diffForHumans() ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
