<div class="w-full px-4 py-6 sm:px-6 md:px-8">
    @php use Illuminate\Support\Carbon; @endphp

    @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    @endpushOnce

    @pushOnce('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
        let currentMap = document.getElementById('currentMap');
        let map = document.getElementById('map');

        setTimeout(() => {
            toggleMap();
            toggleCurrentMap();
        }, 1000);

        function toggleCurrentMap() {
            const mapIsVisible = currentMap.style.display === "none";
            currentMap.style.display = mapIsVisible ? "block" : "none";
            document.querySelector('#toggleCurrentMap').innerHTML = mapIsVisible ?
                `<x-heroicon-s-chevron-up class="mr-2 h-5 w-5" />` :
                `<x-heroicon-s-chevron-down class="mr-2 h-5 w-5" />`;
        }

        function toggleMap() {
            const mapIsVisible = map.style.display === "none";
            map.style.display = mapIsVisible ? "block" : "none";
        }
    </script>
    @endpushOnce

    @if (!$isAbsence)
        <script src="{{ url('/assets/js/html5-qrcode.min.js') }}"></script>
    @endif

    <div class="flex flex-col gap-6 md:flex-row">
        @if (!$isAbsence)
            <!-- Left: Shift & Scanner -->
            <div class="flex flex-col gap-4 w-full md:w-1/2">
                <div>
                    <x-select id="shift" class="mt-1 block w-full" wire:model="shift_id"
                        disabled="{{ !is_null($attendance) }}">
                        <option value="">{{ __('Select Shift') }}</option>
                        @foreach ($shifts as $shift)
                            <option value="{{ $shift->id }}" {{ $shift->id == $shift_id ? 'selected' : '' }}>
                                {{ $shift->name . ' | ' . $shift->start_time . ' - ' . $shift->end_time }}
                            </option>
                        @endforeach
                    </x-select>
                    @error('shift_id')
                        <x-input-error for="shift" class="mt-2" message={{ $message }} />
                    @enderror
                </div>
                <div class="flex justify-center rounded-md border border-dashed border-gray-400 dark:border-slate-600 bg-white dark:bg-gray-800 p-4"
                    wire:ignore>
                    <div id="scanner" class="min-h-72 sm:min-h-96 w-72 sm:w-96 rounded-md">
                    </div>
                </div>
            </div>
        @endif

        <!-- Right: Info & Actions -->
        <div class="w-full">
            <div class="mb-4">
                <h4 id="scanner-error" class="text-lg font-semibold text-red-500 dark:text-red-400" wire:ignore></h4>
                <h4 id="scanner-result" class="hidden text-lg font-semibold text-green-500 dark:text-green-400"
                    wire:ignore>
                    {{ $successMsg }}
                </h4>
                <div class="text-base text-gray-700 dark:text-gray-100 mb-3">
                    {{ __('Date') . ': ' . now()->format('d/m/Y') }}
                </div>

                @if (!is_null($currentLiveCoords))
                    <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-300">
                        <a href="{{ \App\Helpers::getGoogleMapsUrl($currentLiveCoords[0], $currentLiveCoords[1]) }}"
                            target="_blank" class="underline hover:text-blue-500">
                            {{ __('Your location') . ': ' . $currentLiveCoords[0] . ', ' . $currentLiveCoords[1] }}
                        </a>
                        <button class="ml-4 text-gray-600 hover:text-indigo-500" onclick="toggleCurrentMap()"
                            id="toggleCurrentMap">
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
                <div
                    class="bg-blue-500/10 border border-blue-400 dark:border-blue-600 rounded-lg p-5 text-blue-700 dark:text-blue-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02]">
                    <div class="flex items-center gap-3 mb-2">
                        <x-heroicon-o-arrow-down-left class="h-6 w-6 text-blue-500 dark:text-blue-400" />
                        <h4 class="text-lg font-semibold">Absen Masuk</h4>
                    </div>
                    <p class="text-sm">
                        @if ($isAbsence)
                            {{ __($attendance?->status) ?? '-' }}
                        @else
                            <span class="text-sm">
                                {{ $attendance?->time_in ? Carbon::parse($attendance?->time_in)->format('H:i:s') : 'Belum Absen' }}
                            </span>
                        @endif
                    </p>
                    @if ($attendance?->status == 'late')
                        <p class="text-sm mt-1 text-red-600 dark:text-red-300">Terlambat: Ya</p>
                    @endif
                </div>

                <!-- Absen Keluar -->
                <div
                    class="bg-yellow-500/10 border border-yellow-400 dark:border-yellow-600 rounded-lg p-5 text-yellow-700 dark:text-yellow-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02]">
                    <div class="flex items-center gap-3 mb-2">
                        <x-heroicon-o-arrow-up-right class="h-6 w-6 text-yellow-500 dark:text-yellow-400" />
                        <h4 class="text-lg font-semibold">Absen Keluar</h4>
                    </div>
                    <p class="text-sm">
                        @if ($isAbsence)
                            {{ __($attendance?->status) ?? '-' }}
                        @else
                            <span class="text-sm">
                                {{ $attendance?->time_out ? Carbon::parse($attendance?->time_out)->format('H:i:s') : 'Belum Absen' }}
                            </span>
                        @endif
                    </p>
                </div>

                <!-- Koordinat Absen -->
                <button
                    class="bg-purple-500/10 border border-purple-400 dark:border-purple-600 rounded-lg p-5 text-purple-700 dark:text-purple-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02] w-full text-left flex items-start justify-between gap-3"
                    {{ is_null($attendance?->lat_lng) ? 'disabled' : 'onclick=toggleMap()' }} id="toggleMap">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <x-heroicon-o-map-pin class="h-6 w-6 text-purple-500 dark:text-purple-400" />
                            <h4 class="text-lg font-semibold">Koordinat</h4>
                        </div>
                        @if (is_null($attendance?->lat_lng))
                            <p class="text-sm">Belum Absen</p>
                        @else
                            <a href="{{ \App\Helpers::getGoogleMapsUrl($attendance?->latitude, $attendance?->longitude) }}"
                                target="_blank" class="underline hover:text-blue-500 text-sm">
                                {{ $attendance?->latitude . ', ' . $attendance?->longitude }}
                            </a>
                        @endif
                    </div>
                </button>
            </div>

            <hr class="my-6 border-gray-300 dark:border-gray-600">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Ajukan Izin -->
                <a href="{{ route('apply-leave') }}" class="group">
                    <div
                        class="flex items-center justify-center gap-3 rounded-lg bg-amber-500/90 px-5 py-3 text-white font-semibold shadow-md group-hover:bg-amber-600 transition-all duration-200 ease-in-out hover:scale-105">
                        <x-heroicon-o-envelope-open
                            class="h-5 w-5 text-white transition group-hover:rotate-6 duration-300" />
                        <span>Ajukan Izin</span>
                    </div>
                </a>

                <!-- Riwayat Absen -->
                <a href="{{ route('attendance-history') }}" class="group">
                    <div
                        class="flex items-center justify-center gap-3 rounded-lg bg-blue-500/90 px-5 py-3 text-white font-semibold shadow-md group-hover:bg-blue-600 transition-all duration-200 ease-in-out hover:scale-105">
                        <x-heroicon-o-clock class="h-5 w-5 text-white transition group-hover:rotate-6 duration-300" />
                        <span>Riwayat Absen</span>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>


@script
<script>
    const errorMsg = document.querySelector('#scanner-error');
    getLocation();

    async function getLocation() {
        if (navigator.geolocation) {
            const map = L.map('currentMap');
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 21,
            }).addTo(map);
            navigator.geolocation.watchPosition((position) => {
                console.log(position);
                $wire.$set('currentLiveCoords', [position.coords.latitude, position.coords.longitude]);
                map.setView([
                    Number(position.coords.latitude),
                    Number(position.coords.longitude),
                ], 13);
                L.marker([position.coords.latitude, position.coords.longitude]).addTo(map);
            }, (err) => {
                console.error(`ERROR(${err.code}): ${err.message}`);
                alert('{{ __('Please enable your location') }}');
            });
        } else {
            document.querySelector('#scanner-error').innerHTML = "Gagal mendeteksi lokasi";
        }
    }

    if (!$wire.isAbsence) {
        const scanner = new Html5Qrcode('scanner');

        const config = {
            formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE],
            fps: 15,
            aspectRatio: 1,
            qrbox: {
                width: 280,
                height: 280
            },
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
        };

        async function startScanning() {
            if (scanner.getState() === Html5QrcodeScannerState.PAUSED) {
                return scanner.resume();
            }
            await scanner.start({
                facingMode: "environment"
            },
                config,
                onScanSuccess,
            );
        }

        async function onScanSuccess(decodedText, decodedResult) {
            console.log(`Code matched = ${decodedText}`, decodedResult);

            if (scanner.getState() === Html5QrcodeScannerState.SCANNING) {
                scanner.pause(true);
            }

            if (!(await checkTime())) {
                await startScanning();
                return;
            }

            const result = await $wire.scan(decodedText);

            if (result === true) {
                return onAttendanceSuccess();
            } else if (typeof result === 'string') {
                errorMsg.innerHTML = result;
            }

            setTimeout(async () => {
                await startScanning();
            }, 500);
        }

        async function checkTime() {
            const attendance = await $wire.getAttendance();

            if (attendance) {
                const timeIn = new Date(attendance.time_in).valueOf();
                const diff = (Date.now() - timeIn) / (1000 * 3600);
                const minAttendanceTime = 1;
                console.log(`Difference = ${diff}`);
                if (diff <= minAttendanceTime) {
                    const timeIn = new Date(attendance.time_in).toLocaleTimeString([], {
                        hour: 'numeric',
                        minute: 'numeric',
                        second: 'numeric',
                        hour12: false,
                    });
                    const confirmation = confirm(
                        `Anda baru saja absen pada ${timeIn}, apakah ingin melanjutkan untuk absen keluar?`
                    );
                    return confirmation;
                }
            }
            return true;
        }

        function onAttendanceSuccess() {
            scanner.stop();
            errorMsg.innerHTML = '';
            document.querySelector('#scanner-result').classList.remove('hidden');
        }

        const observer = new MutationObserver((mutationList, observer) => {
            const classes = ['text-white', 'bg-blue-500', 'dark:bg-blue-400', 'rounded-md', 'px-3', 'py-1'];
            for (const mutation of mutationList) {
                if (mutation.type === 'childList') {
                    const startBtn = document.querySelector('#html5-qrcode-button-camera-start');
                    const stopBtn = document.querySelector('#html5-qrcode-button-camera-stop');
                    const fileBtn = document.querySelector('#html5-qrcode-button-file-selection');
                    const permissionBtn = document.querySelector('#html5-qrcode-button-camera-permission');

                    if (startBtn) {
                        startBtn.classList.add(...classes);
                        stopBtn.classList.add(...classes, 'bg-red-500');
                        fileBtn.classList.add(...classes);
                    }

                    if (permissionBtn)
                        permissionBtn.classList.add(...classes);
                }
            }
        });

        observer.observe(document.querySelector('#scanner'), {
            childList: true,
            subtree: true,
        });

        const shift = document.querySelector('#shift');
        const msg = 'Pilih shift terlebih dahulu';
        let isRendered = false;
        setTimeout(() => {
            if (!shift.value) {
                errorMsg.innerHTML = msg;
            } else {
                startScanning();
                isRendered = true;
            }
        }, 1000);
        shift.addEventListener('change', () => {
            if (!isRendered) {
                startScanning();
                isRendered = true;
                errorMsg.innerHTML = '';
            }
            if (!shift.value) {
                scanner.pause(true);
                errorMsg.innerHTML = msg;
            } else if (scanner.getState() === Html5QrcodeScannerState.PAUSED) {
                scanner.resume();
                errorMsg.innerHTML = '';
            }
        });

        const map = L.map('map').setView([
            Number({{ $attendance?->latitude }}),
            Number({{ $attendance?->longitude }}),
        ], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 21,
        }).addTo(map);
        L.marker([
            Number({{ $attendance?->latitude }}),
            Number({{ $attendance?->longitude }}),
        ]).addTo(map);
    }
</script>
@endscript
