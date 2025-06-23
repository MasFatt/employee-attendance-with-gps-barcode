<div class="w-full px-4 py-6 sm:px-6 md:px-8">
    @php use Illuminate\Support\Carbon; @endphp

    @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    @endpushOnce

    @pushOnce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    @endpushOnce

    @if (!$isAbsence)
        <script src="{{ url('/assets/js/html5-qrcode.min.js') }}"></script>
    @endif

    <div class="flex flex-col gap-6 md:flex-row">
        @if (!$isAbsence)
            <!-- Left: Shift & Scanner -->
            <div class="flex flex-col gap-4 w-full md:w-1/2">
                <div>
                    <x-select id="shift" class="mt-1 block w-full" wire:model="shift_id" :disabled="!is_null($attendance)">
                        <option value="">{{ __('Select Shift') }}</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}" @selected($shift->id == $shift_id)>
                                {{ $shift->name . ' | ' . $shift->start_time . ' - ' . $shift->end_time }}
                            </option>
                        @endforeach
                    </x-select>
                    @error('shift_id')
                        <x-input-error for="shift" class="mt-2" :message="$message" />
                    @enderror
                </div>
                <div class="flex justify-center rounded-md border border-dashed border-gray-400 dark:border-slate-600 bg-white dark:bg-gray-800 p-4" wire:ignore>
                    <div id="scanner" class="min-h-72 sm:min-h-96 w-72 sm:w-96 rounded-md"></div>
                </div>
            </div>
        @endif

        <!-- Right: Info & Actions -->
        <div class="w-full">
            <div class="mb-4">
                <h4 id="scanner-error" class="text-lg font-semibold text-red-500 dark:text-red-400" wire:ignore></h4>
                <h4 id="scanner-result" class="hidden text-lg font-semibold text-green-500 dark:text-green-400" wire:ignore>
                    {{ $successMsg }}
                </h4>
                <div class="text-base text-gray-700 dark:text-gray-100 mb-3">
                    {{ __('Date') . ': ' . now()->format('d/m/Y') }}
                </div>

                @if (!is_null($currentLiveCoords))
                    <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-300">
                        <a href="{{ \App\Helpers::getGoogleMapsUrl($currentLiveCoords[0], $currentLiveCoords[1]) }}" target="_blank" class="underline hover:text-blue-500">
                            {{ __('Your location') . ': ' . $currentLiveCoords[0] . ', ' . $currentLiveCoords[1] }}
                        </a>
                        <button class="ml-4 text-gray-600 hover:text-indigo-500" onclick="toggleCurrentMap()" id="toggleCurrentMapBtn">
                            <x-heroicon-s-chevron-down class="h-5 w-5" />
                        </button>
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-300">Your location: -, -</p>
                @endif
                <div class="my-4 h-72 md:h-96 hidden" id="currentMap" wire:ignore></div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
                <!-- Absen Masuk -->
                <div class="bg-blue-500/10 border border-blue-400 dark:border-blue-600 rounded-lg p-5 text-blue-700 dark:text-blue-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02]">
                    <div class="flex items-center gap-3 mb-2">
                        <x-heroicon-o-arrow-down-left class="h-6 w-6 text-blue-500 dark:text-blue-400" />
                        <h4 class="text-lg font-semibold">Absen Masuk</h4>
                    </div>
                    <p class="text-sm">
                        @if ($isAbsence)
                            {{ __($attendance?->status ?? '-') }}
                        @else
                            {{ $attendance?->time_in ? Carbon::parse($attendance->time_in)->format('H:i:s') : 'Belum Absen' }}
                        @endif
                    </p>
                    @if ($attendance?->status === 'late')
                        <p class="text-sm mt-1 text-red-600 dark:text-red-300">Terlambat: Ya</p>
                    @endif
                </div>

                <!-- Absen Keluar -->
                <div class="bg-yellow-500/10 border border-yellow-400 dark:border-yellow-600 rounded-lg p-5 text-yellow-700 dark:text-yellow-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02]">
                    <div class="flex items-center gap-3 mb-2">
                        <x-heroicon-o-arrow-up-right class="h-6 w-6 text-yellow-500 dark:text-yellow-400" />
                        <h4 class="text-lg font-semibold">Absen Keluar</h4>
                    </div>
                    <p class="text-sm">
                        @if ($isAbsence)
                            {{ __($attendance?->status ?? '-') }}
                        @else
                            {{ $attendance?->time_out ? Carbon::parse($attendance->time_out)->format('H:i:s') : 'Belum Absen' }}
                        @endif
                    </p>
                </div>

                <!-- Koordinat Absen -->
                <button
                    type="button"
                    class="bg-purple-500/10 border border-purple-400 dark:border-purple-600 rounded-lg p-5 text-purple-700 dark:text-purple-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02] w-full text-left flex items-start justify-between gap-3"
                    @if (is_null($attendance?->latitude) || is_null($attendance?->longitude))
                        disabled
                    @else
                        onclick="toggleMap()"
                    @endif
                    id="toggleMapBtn"
                >
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <x-heroicon-o-map-pin class="h-6 w-6 text-purple-500 dark:text-purple-400" />
                            <h4 class="text-lg font-semibold">Koordinat</h4>
                        </div>
                        @if (is_null($attendance?->latitude) || is_null($attendance?->longitude))
                            <p class="text-sm">Belum Absen</p>
                        @else
                            <a href="{{ \App\Helpers::getGoogleMapsUrl($attendance->latitude, $attendance->longitude) }}" target="_blank" class="underline hover:text-blue-500 text-sm">
                                {{ $attendance->latitude . ', ' . $attendance->longitude }}
                            </a>
                        @endif
                    </div>
                </button>
            </div>

            <hr class="my-6 border-gray-300 dark:border-gray-600">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Ajukan Izin -->
                <a href="{{ route('apply-leave') }}" class="group">
                    <div class="flex items-center justify-center gap-3 rounded bg-amber-500/90 px-4 py-2 text-white font-semibold shadow-md group-hover:bg-amber-600">
                        {{-- <x-heroicon-o-envelope-open class="h-5 w-5 text-white transition group-hover:rotate-6 duration-300" /> --}}
                        <span>Ajukan Izin</span>
                    </div>
                </a>

                <!-- Riwayat Absen -->
                <a href="{{ route('attendance-history') }}" class="group">
                    <div class="flex items-center justify-center gap-3 rounded bg-blue-500/90 px-4 py-2 text-white font-semibold shadow-md group-hover:bg-blue-600">
                        {{-- <x-heroicon-o-clock class="h-5 w-5 text-white transition group-hover:rotate-6 duration-300" /> --}}
                        <span>Riwayat Absen</span>
                    </div>
                </a>

                <!-- Upload File QR -->
                <input type="file" id="qr-file-input" accept="image/*" class="hidden" />
                <button type="button" id="custom-upload-btn" class="block md:hidden px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700 transition">Upload File QR</button>
            </div>
        </div>
    </div>

    {{-- Elemen dummy untuk Html5Qrcode scanFile --}}
    <div id="qr-file-scanner" style="display:none;"></div>
</div>

@push('scripts')
<script>
    // Toggle currentMap visibility and icon
    function toggleCurrentMap() {
        const currentMap = document.getElementById('currentMap');
        const toggleBtn = document.getElementById('toggleCurrentMapBtn');
        const isHidden = currentMap.classList.contains('hidden');

        if (isHidden) {
            currentMap.classList.remove('hidden');
            toggleBtn.innerHTML = `<x-heroicon-s-chevron-up class="mr-2 h-5 w-5" />`;
        } else {
            currentMap.classList.add('hidden');
            toggleBtn.innerHTML = `<x-heroicon-s-chevron-down class="mr-2 h-5 w-5" />`;
        }
    }

    // Toggle attendance map visibility
    function toggleMap() {
        const map = document.getElementById('map');
        if (!map) return;
        const isHidden = map.classList.contains('hidden');
        if (isHidden) {
            map.classList.remove('hidden');
        } else {
            map.classList.add('hidden');
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        const qrInput = document.getElementById("qr-file-input");
        const customUploadBtn = document.getElementById("custom-upload-btn");
        const errorMsg = document.getElementById('scanner-error');
        const successMsg = document.getElementById('scanner-result');

        customUploadBtn?.addEventListener("click", () => {
            qrInput?.click();
        });

        qrInput?.addEventListener("change", async (e) => {
            if (e.target.files.length === 0) return;

            const file = e.target.files[0];
            const html5QrCode = new Html5Qrcode('qr-file-scanner');

            try {
                const decodedText = await html5QrCode.scanFile(file, true);
                console.log("Decoded QR from file:", decodedText);

                const scanResult = await @this.scan(decodedText);

                if (scanResult === true) {
                    successMsg.classList.remove('hidden');
                    errorMsg.innerHTML = '';
                } else if (typeof scanResult === 'string') {
                    errorMsg.innerHTML = scanResult;
                    successMsg.classList.add('hidden');
                }

                await html5QrCode.clear();
            } catch (err) {
                console.error("Failed to decode QR from file", err);
                errorMsg.innerHTML = "Gagal membaca file QR";
                successMsg.classList.add('hidden');
            }
        });

        // Geolocation & Map Initialization
        if (navigator.geolocation) {
            const currentMap = L.map('currentMap');
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 21,
            }).addTo(currentMap);

            navigator.geolocation.watchPosition((position) => {
                @this.set('currentLiveCoords', [position.coords.latitude, position.coords.longitude]);
                currentMap.setView([position.coords.latitude, position.coords.longitude], 13);
                L.marker([position.coords.latitude, position.coords.longitude]).addTo(currentMap);
            }, (err) => {
                console.error(`ERROR(${err.code}): ${err.message}`);
                alert('{{ __('Please enable your location') }}');
            });
        } else {
            errorMsg.innerHTML = "Gagal mendeteksi lokasi";
        }

        @if (!$isAbsence)
        // QR Scanner Initialization
        const scanner = new Html5Qrcode('scanner');
        const errorMsgLive = document.getElementById('scanner-error');

        const config = {
            formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
            fps: 15,
            aspectRatio: 1,
            qrbox: { width: 280, height: 280 },
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
        };

        function startScanner() {
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    let cameraId = cameras[0].id;
                    scanner.start(cameraId, config, async (decodedText) => {
                        console.log(`QR decoded: ${decodedText}`);
                        const res = await @this.scan(decodedText);
                        if (res === true) {
                            errorMsgLive.textContent = '';
                        } else if (typeof res === 'string') {
                            errorMsgLive.textContent = res;
                        }
                    }).catch(err => {
                        errorMsgLive.textContent = 'Failed to start scanner: ' + err;
                    });
                } else {
                    errorMsgLive.textContent = 'Camera not found';
                }
            }).catch(err => {
                errorMsgLive.textContent = 'Camera access denied: ' + err;
            });
        }

        startScanner();
        @endif
    });
</script>
@endpush