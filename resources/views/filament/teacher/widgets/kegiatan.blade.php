@php
    $activities = $this->getActivities();
@endphp

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            📋 Kegiatan Pondok Pesantren Mendatang
        </x-slot>

        @if($activities->isEmpty())
            <div class="flex flex-col items-center justify-center py-6 text-center">
                <x-heroicon-o-calendar-days class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" />
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada kegiatan mendatang</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($activities as $activity)
                    @php
                        $date = \Carbon\Carbon::parse($activity->activity_date);
                        $isToday = $date->isToday();
                        $isTomorrow = $date->isTomorrow();
                        $daysLeft = (int) now()->startOfDay()->diffInDays($date->startOfDay(), false);

                        if ($isToday) {
                            $badgeColor = 'danger';
                            $badgeText = 'Hari Ini!';
                            $icon = '🔴';
                        } elseif ($isTomorrow) {
                            $badgeColor = 'warning';
                            $badgeText = 'Besok';
                            $icon = '🟡';
                        } elseif ($daysLeft <= 7) {
                            $badgeColor = 'info';
                            $badgeText = $daysLeft . ' hari lagi';
                            $icon = '🔵';
                        } else {
                            $badgeColor = 'gray';
                            $badgeText = $date->translatedFormat('d M Y');
                            $icon = '⚪';
                        }
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-lg">{{ $icon }}</span>
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 dark:text-white truncate">{{ $activity->activity_name }}</p>
                                @if($activity->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Str::limit($activity->description, 60) }}</p>
                                @endif
                            </div>
                        </div>
                        <x-filament::badge :color="$badgeColor" size="sm">
                            {{ $badgeText }}
                        </x-filament::badge>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
