@php
    $date = Carbon\Carbon::now();
@endphp
<div>
    @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpushOnce
    <div class="flex flex-col gap-4 mb-6">
        <div class="text-left">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Absensi Hari Ini</h3>
            <p id="tanggal-jam" class="text-sm text-gray-500 dark:text-gray-400 tracking-wide mt-1 font-mono"></p>

            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mt-4">Jumlah Karyawan:</h3>
            <span class="block text-sm font-normal mt-1">{{ $employeesCount }}</span>
        </div>

        <script>
            function formatTanggalWaktu(date) {
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                const tanggal = date.toLocaleDateString('id-ID', options);
                const jam = date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                return `${tanggal}, ${jam}`;
            }

            function updateTanggalJam() {
                const sekarang = new Date();
                document.getElementById('tanggal-jam').textContent = formatTanggalWaktu(sekarang);
            }

            updateTanggalJam();
            setInterval(updateTanggalJam, 1000);
        </script>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card Hadir -->
            <div
                class="bg-emerald-500/10 border border-emerald-400 dark:border-emerald-600 rounded-lg p-5 text-emerald-700 dark:text-emerald-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02]">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-6 h-6 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <h4 class="text-lg font-semibold">Hadir</h4>
                </div>
                <p class="text-3xl font-bold">{{ $presentCount }}</p>
                <p class="text-sm mt-1">Terlambat: {{ $lateCount }}</p>
            </div>

            <!-- Card Izin -->
            <div
                class="bg-sky-500/10 border border-sky-400 dark:border-sky-600 rounded-lg p-5 text-sky-700 dark:text-sky-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02]">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-6 h-6 text-sky-500 dark:text-sky-400" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3M12 19c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z" />
                    </svg>
                    <h4 class="text-lg font-semibold">Izin</h4>
                </div>
                <p class="text-3xl font-bold">{{ $excusedCount }}</p>
                <p class="text-sm mt-1">Izin/Cuti</p>
            </div>

            <!-- Card Sakit -->
            <div
                class="bg-purple-500/10 border border-purple-400 dark:border-purple-600 rounded-lg p-5 text-purple-700 dark:text-purple-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02]">
                <div class="flex items-center gap-3 mb-2">
                    <!-- Medical Cross Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-purple-500 dark:text-purple-400"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                            clip-rule="evenodd" />
                    </svg>
                    <h4 class="text-lg font-semibold">Sakit</h4>
                </div>
                <p class="text-3xl font-bold">{{ $sickCount }}</p>
            </div>

            <!-- Card Tidak Hadir -->
            <div
                class="bg-rose-500/10 border border-rose-400 dark:border-rose-600 rounded-lg p-5 text-rose-700 dark:text-rose-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02]">
                <div class="flex items-center gap-3 mb-2">
                    <svg class="w-6 h-6 text-rose-500 dark:text-rose-400" fill="none" stroke="currentColor"
                        stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <h4 class="text-lg font-semibold">Tidak Hadir</h4>
                </div>
                <p class="text-3xl font-bold">{{ $absentCount }}</p>
                <p class="text-sm mt-1">Tidak/Belum Hadir</p>
            </div>
        </div>

        <div class="mb-4 overflow-x-scroll">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            {{ __('Name') }}
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            {{ __('NIP') }}
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            {{ __('Division') }}
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            {{ __('Job Title') }}
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            {{ __('Shift') }}
                        </th>
                        <th scope="col"
                            class="text-nowrap px-1 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300">
                            Status
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            {{ __('Time In') }}
                        </th>
                        <th scope="col"
                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                            {{ __('Time Out') }}
                        </th>
                        <th scope="col" class="relative">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                    @php
                        $class = 'px-4 py-3 text-sm font-medium text-gray-900 dark:text-white';
                    @endphp
                    @foreach ($employees as $employee)
                        @php
                            $attendance = $employee->attendance;
                            $timeIn = $attendance ? $attendance?->time_in?->format('H:i:s') : null;
                            $timeOut = $attendance ? $attendance?->time_out?->format('H:i:s') : null;
                            $isWeekend = $date->isWeekend();
                            $status = ($attendance ?? [
                                'status' => $isWeekend || !$date->isPast() ? '-' : 'absent',
                            ])['status'];
                            switch ($status) {
                                case 'present':
                                    $shortStatus = 'H';
                                    $bgColor =
                                        'bg-green-200 dark:bg-green-800 hover:bg-green-300 dark:hover:bg-green-700 border border-green-300 dark:border-green-600';
                                    break;
                                case 'late':
                                    $shortStatus = 'T';
                                    $bgColor =
                                        'bg-amber-200 dark:bg-amber-800 hover:bg-amber-300 dark:hover:bg-amber-700 border border-amber-300 dark:border-amber-600';
                                    break;
                                case 'excused':
                                    $shortStatus = 'I';
                                    $bgColor =
                                        'bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 border border-blue-300 dark:border-blue-600';
                                    break;
                                case 'sick':
                                    $shortStatus = 'S';
                                    $bgColor = 'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                                    break;
                                case 'absent':
                                    $shortStatus = 'A';
                                    $bgColor =
                                        'bg-red-200 dark:bg-red-800 hover:bg-red-300 dark:hover:bg-red-700 border border-red-300 dark:border-red-600';
                                    break;
                                default:
                                    $shortStatus = '-';
                                    $bgColor = 'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                                    break;
                            }
                          @endphp
                        <tr wire:key="{{ $employee->id }}" class="group">
                            {{-- Detail karyawan --}}
                            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                                {{ $employee->name }}
                            </td>
                            <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                                {{ $employee->nip }}
                            </td>
                            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                                {{ $employee->division?->name ?? '-' }}
                            </td>
                            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                                {{ $employee->jobTitle?->name ?? '-' }}
                            </td>
                            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                                {{ $attendance->shift?->name ?? '-' }}
                            </td>

                            {{-- Absensi --}}
                            <td
                                class="{{ $bgColor }} text-nowrap px-1 py-3 text-center text-sm font-medium text-gray-900 dark:text-white">
                                {{ __($status) }}
                            </td>

                            {{-- Waktu masuk/keluar --}}
                            <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                                {{ $timeIn ?? '-' }}
                            </td>
                            <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                                {{ $timeOut ?? '-' }}
                            </td>

                            {{-- Action --}}
                            <td
                                class="cursor-pointer text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:text-white dark:group-hover:bg-gray-700">
                                <div class="flex items-center justify-center gap-3">
                                    @if ($attendance && ($attendance->attachment || $attendance->note || $attendance->lat_lng))
                                        <x-button type="button" wire:click="show({{ $attendance->id }})"
                                            onclick="setLocation({{ $attendance->latitude ?? 0 }}, {{ $attendance->longitude ?? 0 }})">
                                            {{ __('Detail') }}
                                        </x-button>
                                    @else
                                        -
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $employees->links() }}

        <x-attendance-detail-modal :current-attendance="$currentAttendance" />
        @stack('attendance-detail-scripts')
    </div>