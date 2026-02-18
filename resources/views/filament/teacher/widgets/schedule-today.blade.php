@php
    $schedules = $this->getSchedules();
    $today = $this->getToday();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📅 Jadwal Mengajar Hari Ini — {{ $today }}
        </x-slot>

        @if($schedules->isEmpty())
            <div class="flex flex-col items-center justify-center py-6 text-center">
                <x-heroicon-o-calendar class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" />
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tidak ada jadwal mengajar hari ini</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Jam</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300">Kelas</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($schedules as $schedule)
                            @php
                                $now = now()->format('H:i');
                                $start = \Carbon\Carbon::parse($schedule->start_time)->format('H:i');
                                $end = \Carbon\Carbon::parse($schedule->end_time)->format('H:i');

                                if ($now < $start) {
                                    $status = 'Belum Mulai';
                                    $statusColor = 'warning';
                                } elseif ($now >= $start && $now <= $end) {
                                    $status = 'Berlangsung';
                                    $statusColor = 'success';
                                } else {
                                    $status = 'Selesai';
                                    $statusColor = 'gray';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-m-clock class="w-4 h-4 text-primary-500" />
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $start }}</span>
                                        <span class="text-gray-400">—</span>
                                        <span class="text-gray-600 dark:text-gray-400">{{ $end }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-m-book-open class="w-4 h-4 text-gray-400" />
                                        <span class="font-semibold text-gray-900 dark:text-white">{{ $schedule->lesson?->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <x-filament::badge color="primary" size="sm">
                                        {{ $schedule->classes?->class_name ?? '-' }}
                                    </x-filament::badge>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <x-filament::badge :color="$statusColor" size="sm">
                                        {{ $status }}
                                    </x-filament::badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
