<x-filament-panels::page>
    <div
        x-load-js="[@js(\Filament\Support\Facades\FilamentAsset::getScriptSrc('html5-qrcode'))]"
        x-data="{
            scanner: new Html5QrcodeScanner(
                'reader',
                { fps: 10, qrbox: {width: 250, height: 250}, formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ] },
                false
            ),
            init() {
                this.scanner.render(this.onQRScanSuccess);

                //$nextTick(() => {
                //    $wire.on('resume-scan', () => {
                //        console.log(this.scanner.getState());
                //    });
                //})
            },
            onQRScanSuccess(decodedText, decodedResult) {
                //$dispatch('scan-success', { data: decodedText })
                //this.scanner.stop();
            }
        }"
    >
        <div id="reader" width="600px"></div>
    </div>
</x-filament-panels::page>
