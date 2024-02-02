<x-filament-panels::page>
    <div
        x-data="{
            decoded: '',
            scannerConfig: {
                fps: 10,
                qrbox: {width: 250,height: 250},
                rememberLastUsedCamera: true,
                showTorchButtonIfSupported: true,
                formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
                supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
            },
            init() {
                let v = this;
                let scanner = new Html5QrcodeScanner(
                    'reader',
                    this.scannerConfig,
                    false
                );
                function onQRScanSuccess(decodedText, decodedResult) {
                    if (decodedText !== v.decoded) {
                        $dispatch('scan', { data: decodedText });
                        scanner.pause();
                    }

                    v.decoded = decodedText;
                }

                $wire.on('resume-scan', () => {
                    setTimeout(() => { v.decoded = '' }, 1000);
                    scanner.resume();
                })

                scanner.render(onQRScanSuccess);
            }
        }"
    >
        <div class="grid gap-4">
            <x-filament::section>
                <x-slot name="heading">
                    Scan Kode QR
                </x-slot>

                <div class="block max-w-xl mx-auto mb-5">
                    <div id="reader" wire:ignore></div>
                </div>

                <x-filament::section x-show="decoded">
                    <x-slot name="heading">
                        Hasil Scan
                    </x-slot>

                    <x-slot name="headerActions">
                        <x-filament::button
                            @click="$dispatch('resume-scan')"
                        >
                            Lanjut Scan
                        </x-filament::button>
                    </x-slot>

                    <p x-text="decoded"></p>
                </x-filament::section>
            </x-filament::section>

            <x-filament::section collapsible collapsed>
                <x-slot name="heading">
                    Gerbang Terdaftar
                </x-slot>

                <div class="grid gap-4 grid-cols-1 md:grid-cols-3 lg:grid-cols-4" wire:ignore>
                    @forelse($this->staffGates as $gate)
                        <x-filament::section>
                            <x-slot name="heading">
                                {{ $gate->name }}
                            </x-slot>

                            <p>
                                {!! $gate->categories->pluck('label')->join('<br />') !!}
                            </p>
                        </x-filament::section>
                    @empty
                        <p>Anda tidak terdaftar di gerbang manapun</p>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        <x-filament::modal id="scan-result" width="3xl">
            <div>
                {{ $this->visitorInfolist }}
            </div>

            <div>
                <form wire:submit="checkIn">
                    {{ $this->form }}
                </form>
            </div>
        </x-filament::modal>
    </div>
</x-filament-panels::page>
