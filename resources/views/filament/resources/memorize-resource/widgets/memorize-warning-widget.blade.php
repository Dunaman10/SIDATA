<x-filament-widgets::widget>
  <x-filament::section>
    <div class="flex items-start gap-4 p-4 rounded-xl border border-warning-500/40 bg-warning-50/50 dark:bg-warning-950/20">
      {{-- Icon --}}
      <div class="shrink-0 mt-0.5">
        <x-heroicon-s-exclamation-triangle class="w-8 h-8 text-warning-500" />
      </div>

      {{-- Content --}}
      <div class="flex-1 space-y-1">
        <h3 class="text-base font-bold text-warning-700 dark:text-warning-400">
          ⚠️ Peringatan Auto-Hapus Data
        </h3>

        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
          Untuk menjaga performa dan kapasitas penyimpanan server, <strong>seluruh data setoran hafalan</strong> (termasuk file Audio & Foto) akan <span class="font-bold text-danger-600 dark:text-danger-400">dihapus secara otomatis dan permanen</span> setiap pergantian semester.
        </p>

        <div class="mt-3 grid grid-cols-1 sm:grid-cols-3 gap-3">
          <div class="rounded-lg bg-white dark:bg-gray-800 px-4 py-2 border border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">Total Data Hafalan</p>
            <p class="text-lg font-bold text-primary-600 dark:text-primary-400">{{ $this->totalRecords }} record</p>
          </div>

          <div class="rounded-lg bg-white dark:bg-gray-800 px-4 py-2 border border-gray-200 dark:border-gray-700">
            <p class="text-xs text-gray-500 dark:text-gray-400">Semester Aktif</p>
            <p class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $this->semesterLabel }}</p>
          </div>

          <div class="rounded-lg px-4 py-2 border
            {{ $this->daysLeft <= 30
                ? 'bg-danger-50 dark:bg-danger-950/30 border-danger-300 dark:border-danger-700'
                : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700' }}">
            <p class="text-xs text-gray-500 dark:text-gray-400">Penghapusan pada {{ $this->deleteDate }}</p>
            <p class="text-lg font-bold {{ $this->daysLeft <= 30 ? 'text-danger-600 dark:text-danger-400' : 'text-gray-800 dark:text-gray-200' }}">
              @if($this->daysLeft <= 0)
                Segera dihapus!
              @else
                {{ $this->daysLeft }} hari lagi
              @endif
            </p>
          </div>
        </div>

        <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 italic">
          💡 Pastikan Anda sudah mendownload arsip data hafalan secara berkala dengan menekan tombol <strong>"Download Arsip Hafalan"</strong> di atas sebelum data terhapus.
        </p>
      </div>
    </div>
  </x-filament::section>
</x-filament-widgets::widget>
