<div>
    @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpushOnce
    <h3 class="col-span-2 mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
        Data Absensi
    </h3>
    <!-- Bulan -->
    <div class="flex flex-col w-40">
        <x-label for="month" value="Per Bulan" />
        <x-input type="month" name="month" id="month" wire:model.live="month" class="mt-1" />
    </div>
    <h5 class="mt-3 text-sm text-red-600 italic ">*Klik pada tanggal untuk melihat detail</h5>
    <div class="mt-4 flex w-full flex-col gap-6 lg:flex-row lg:gap-8">

        <!-- Kalender 50% -->
        <div class="w-full lg:w-1/2 px-4">
          <!-- Header Hari -->
          <div class="grid grid-cols-7 text-center text-sm font-semibold text-gray-500 dark:text-gray-400 select-none">
            @foreach (['M', 'S', 'S', 'R', 'K', 'J', 'S'] as $day)
              <div
                class="py-2 rounded-t-md
                {{ $day === 'M' ? 'text-red-500 font-bold' : '' }}
                {{ $day === 'J' ? 'text-green-600 dark:text-green-400 font-semibold' : '' }}">
                {{ $day }}
              </div>
            @endforeach
          </div>

          <!-- Tanggal -->
          <div class="grid grid-cols-7 gap-1 bg-white dark:bg-gray-900 rounded-b-md shadow-inner p-1">
            @if ($start->dayOfWeek !== 0)
              @foreach (range(1, $start->dayOfWeek) as $i)
                <div class="aspect-square rounded-md bg-gray-100 dark:bg-gray-800"></div>
              @endforeach
            @endif

            @php
              $presentCount = 0;
              $lateCount = 0;
              $excusedCount = 0;
              $sickCount = 0;
              $absentCount = 0;
            @endphp
            @foreach ($dates as $date)
              @php
                $isWeekend = $date->isWeekend();
                $attendance = $attendances->firstWhere(fn($v, $k) => $v['date'] === $date->format('Y-m-d'));
                $status = ($attendance ?? [
                    'status' => $isWeekend || !$date->isPast() ? '-' : 'absent',
                ])['status'];

                switch ($status) {
                  case 'present':
                    $bgColor = 'bg-green-50 dark:bg-green-900 border-green-400 dark:border-green-700 text-green-700 dark:text-green-300';
                    $presentCount++;
                    break;
                  case 'late':
                    $bgColor = 'bg-amber-50 dark:bg-amber-900 border-amber-400 dark:border-amber-700 text-amber-700 dark:text-amber-300';
                    $lateCount++;
                    break;
                  case 'excused':
                    $bgColor = 'bg-sky-50 dark:bg-sky-900 border-sky-400 dark:border-sky-700 text-sky-700 dark:text-sky-300';
                    $excusedCount++;
                    break;
                  case 'sick':
                    $bgColor = 'bg-purple-50 dark:bg-purple-900 border-purple-400 dark:border-purple-700 text-purple-700 dark:text-purple-300';
                    $sickCount++;
                    break;
                  case 'absent':
                    $bgColor = 'bg-red-50 dark:bg-red-900 border-red-400 dark:border-red-700 text-red-700 dark:text-red-300';
                    $absentCount++;
                    break;
                  default:
                    $bgColor = 'bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400';
                    break;
                }
              @endphp
              @if ($attendance && ($attendance['attachment'] || $attendance['note'] || $attendance['coordinates']))
                <button
                  wire:click="show({{ $attendance['id'] }})"
                  onclick="setLocation({{ $attendance['lat'] ?? 0 }}, {{ $attendance['lng'] ?? 0 }})"
                  class="aspect-square rounded-md border p-2 flex flex-col cursor-pointer hover:shadow-lg transition-shadow shadow-sm
                    {{ $bgColor }}">
                  <div class="flex justify-between text-xs font-semibold select-none">
                    <span
                      class="{{ $date->isSunday() ? 'text-red-500' : '' }} {{ $date->isFriday() ? 'text-green-600 dark:text-green-400' : '' }}">
                      {{ $date->format('d') }}
                    </span>
                    <span class="font-bold">
                      @switch($status)
                        @case('present') H @break
                        @case('late') T @break
                        @case('excused') I @break
                        @case('sick') S @break
                        @case('absent') A @break
                        @default - @break
                      @endswitch
                    </span>
                  </div>
                </button>
              @else
                <div
                  class="aspect-square rounded-md border p-2 flex flex-col
                    {{ $bgColor }}">
                  <div class="flex justify-between text-xs font-semibold select-none">
                    <span
                      class="{{ $date->isSunday() ? 'text-red-500' : '' }} {{ $date->isFriday() ? 'text-green-600 dark:text-green-400' : '' }}">
                      {{ $date->format('d') }}
                    </span>
                    <span class="font-bold">
                      @switch($status)
                        @case('present') H @break
                        @case('late') T @break
                        @case('excused') I @break
                        @case('sick') S @break
                        @case('absent') A @break
                        @default - @break
                      @endswitch
                    </span>
                  </div>
                </div>
              @endif
            @endforeach

            @if ($end->dayOfWeek !== 6)
              @foreach (range($end->dayOfWeek + 1, 6) as $i)
                <div class="aspect-square rounded-md bg-gray-100 dark:bg-gray-800"></div>
              @endforeach
            @endif
          </div>
        </div>

        <!-- Keterangan 50% -->
        <div class="w-full lg:w-1/2 px-4 flex flex-col gap-6">
          <!-- Hadir -->
          <div
            class="bg-emerald-500/10 border border-emerald-400 dark:border-emerald-600 rounded-lg p-6 text-emerald-700 dark:text-emerald-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02] flex flex-col justify-between">
            <h4 class="text-lg font-semibold md:text-xl mb-1">Hadir: {{ $presentCount + $lateCount }}</h4>
            <p class="text-sm">Terlambat: {{ $lateCount }}</p>
          </div>

          <!-- Izin -->
          <div
            class="bg-sky-500/10 border border-sky-400 dark:border-sky-600 rounded-lg p-6 text-sky-700 dark:text-sky-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02] flex flex-col justify-center">
            <h4 class="text-lg font-semibold md:text-xl">Izin: {{ $excusedCount }}</h4>
          </div>

          <!-- Sakit -->
          <div
            class="bg-purple-500/10 border border-purple-400 dark:border-purple-600 rounded-lg p-6 text-purple-700 dark:text-purple-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02] flex flex-col justify-center">
            <h4 class="text-lg font-semibold md:text-xl">Sakit: {{ $sickCount }}</h4>
          </div>

          <!-- Absen -->
          <div
            class="bg-rose-500/10 border border-rose-400 dark:border-rose-600 rounded-lg p-6 text-rose-700 dark:text-rose-300 shadow hover:shadow-md transition duration-300 hover:scale-[1.02] flex flex-col justify-center">
            <h4 class="text-lg font-semibold md:text-xl">Absen: {{ $absentCount }}</h4>
          </div>
        </div>

      </div>


        <x-attendance-detail-modal :current-attendance="$currentAttendance" />
        @stack('attendance-detail-scripts')
    </div>