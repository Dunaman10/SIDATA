<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>

    <div
        wire:ignore
        x-data="{
            recorder: null,
            chunks: [],
            recording: false,
            uploading: false,
            timer: 0,
            hasNewRecord: false,
            interval: null,

            start() {
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Browser atau perangkat Anda tidak mendukung fitur rekam suara langsung. Pastikan Anda menggunakan HTTPS dan browser versi terbaru.');
                    return;
                }
                navigator.mediaDevices.getUserMedia({ audio: true })
                    .then(stream => {
                        this.recorder = new MediaRecorder(stream);
                        this.recorder.start();
                        this.recording = true;
                        this.chunks = [];
                        this.timer = 0;

                        this.interval = setInterval(() => this.timer++, 1000);

                        this.recorder.ondataavailable = e => {
                            this.chunks.push(e.data);
                        };

                        this.recorder.onstop = () => {
                            clearInterval(this.interval);

                            const blob = new Blob(this.chunks, { type: 'audio/wav' });

                            const file = new File(
                                [blob],
                                'rekaman-' + Date.now() + '.wav',
                                { type: 'audio/wav' }
                            );

                            this.uploading = true;

                            $wire.upload('{{ $field->getStatePath() }}', file, () => {
                                this.uploading = false;
                                this.hasNewRecord = true;
                                this.$refs.player.src = URL.createObjectURL(blob);
                            }, () => {
                                this.uploading = false;
                                alert('Gagal menyimpan rekaman!');
                            }, (event) => {
                                // Progress callback if needed
                            });

                            this.chunks = [];
                        };
                    })
                    .catch(() => alert('Mic tidak diizinkan.'));
            },

            stop() {
                this.recording = false;
                this.recorder.stop();
            }
        }"
        class="space-y-2"
    >
        {{-- Tombol Rekam --}}
        <button
            x-show="!recording && !uploading"
            x-on:click="start()"
            type="button"
            class="px-4 py-2 bg-primary-600 text-white rounded-lg flex items-center justify-center transition hover:bg-primary-500"
        >
            <span x-show="!hasNewRecord && !@js($field->getState())">🎙 Mulai Rekam</span>
            <span x-show="hasNewRecord || @js($field->getState())">🎙 Rekam Ulang</span>
        </button>

        {{-- Tombol Stop --}}
        <button
            x-show="recording"
            x-on:click="stop()"
            type="button"
            class="px-4 py-2 bg-danger-600 text-white rounded-lg flex items-center justify-center animate-pulse"
        >
            ⛔ Stop ( <span x-text="timer" class="mx-1"></span>s )
        </button>

        {{-- Indikator Loading --}}
        <div x-show="uploading" class="flex items-center space-x-2 text-primary-600 mt-2">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm font-medium">Menyimpan rekaman...</span>
        </div>

        {{-- Player Rekaman --}}
        <div x-show="hasNewRecord || @js($field->getState())" x-cloak>
            <audio x-ref="player" controls class="w-full mt-2" 
                @if($field->getState() && is_string($field->getState()))
                    src="{{ Storage::url($field->getState()) }}"
                @endif
            ></audio>
        </div>

    </div>
</x-dynamic-component>
