<div>
  <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:gap-6">
    @if ($mode != 'import')
      <div>
        <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
          Ekspor Data Absensi
        </h3>
        <form wire:submit.prevent="export">
            <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Tahun -->
                <div class="flex flex-col">
                  <x-label for="year" value="Per Tahun" />
                  <x-input
                    type="number"
                    min="1970"
                    max="2099"
                    name="year"
                    id="year"
                    wire:model.live="year"
                    class="mt-1"
                  />
                </div>

                <!-- Bulan -->
                <div class="flex flex-col">
                  <x-label for="month" value="Per Bulan" />
                  <x-input
                    type="month"
                    name="month"
                    id="month"
                    wire:model.live="month"
                    class="mt-1"
                  />
                </div>

                <!-- Hari -->
                <div class="flex flex-col">
                  <x-label for="day" value="Per Hari" />
                  <x-input
                    type="date"
                    name="day"
                    id="day"
                    wire:model.live="day"
                    class="mt-1"
                  />
                </div>
              </div>

              <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
              </div>

              <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Division -->
                <div class="flex flex-col">
                  <x-label for="division" value="Divisi" />
                  <x-select id="division" name="division" wire:model.live="division" class="mt-1">
                    <option value="">{{ __('Pilih Divisi') }}</option>
                    @foreach (App\Models\Division::all() as $division)
                      <option value="{{ $division->id }}">{{ $division->name }}</option>
                    @endforeach
                  </x-select>
                </div>

                <!-- Job Title -->
                <div class="flex flex-col">
                  <x-label for="jobTitle" value="Jabatan" />
                  <x-select id="jobTitle" name="job_title" wire:model.live="job_title" class="mt-1">
                    <option value="">{{ __('Pilih Jabatan') }}</option>
                    @foreach (App\Models\JobTitle::all() as $jobTitle)
                      <option value="{{ $jobTitle->id }}">{{ $jobTitle->name }}</option>
                    @endforeach
                  </x-select>
                </div>

                <!-- Education -->
                <div class="flex flex-col">
                  <x-label for="education" value="Pendidikan" />
                  <x-select id="education" name="education" wire:model.live="education" class="mt-1">
                    <option value="">{{ __('Pilih Pendidikan') }}</option>
                    @foreach (App\Models\Education::all() as $education)
                      <option value="{{ $education->id }}">{{ $education->name }}</option>
                    @endforeach
                  </x-select>
                </div>
              </div>

          <div class="flex flex-col items-center justify-stretch gap-4">
            <x-secondary-button type="button" wire:click="preview" class="w-full justify-center">
              @if ($mode == 'export')
                {{ __('Cancel') }}
              @else
                {{ __('Preview') }}
              @endif
            </x-secondary-button>
            <x-button class="w-full justify-center" wire:loading.attr="disabled">
              {{ __('Export') }}
            </x-button>
          </div>
        </form>
      </div>
    @endif
    @if ($mode != 'export')
<div class="max-w-2xl pl-4 p-0 space-y-6">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Impor Data Absensi</h3>
        <form x-data="{ file: null }" wire:submit.prevent="import" method="post" enctype="multipart/form-data" class="space-y-6">
          @csrf

          <!-- File Picker -->
          <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Upload File Absensi</label>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
              <!-- Pilih/Ganti File -->
              <x-secondary-button type="button" x-on:click.prevent="$refs.file.click()" class="sm:w-auto w-full">
                <span x-text="file ? 'Ganti File' : 'Pilih File dan Pratinjau'"></span>
              </x-secondary-button>

              <!-- Hapus File -->
              <x-secondary-button type="button" x-show="file" x-on:click.prevent="
                $refs.file.value = null;
                file = null;
                $wire.set('file', null);
              " class="sm:w-auto w-full">
                Hapus File
              </x-secondary-button>
            </div>

            <!-- Nama File -->
            <div class="text-sm text-red-600 dark:text-red-600 italic" x-text="file ? file.name : '*Belum ada file yang dipilih'"></div>

            <!-- Hidden Input -->
            <x-input type="file" class="hidden" x-ref="file" x-on:change="file = $refs.file.files[0]" wire:model.live="file" />
          </div>

          <!-- Tombol Impor -->
          <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
            <x-danger-button type="submit" class="w-full sm:w-auto justify-center">
              <span x-text="file ? 'Import: ' + file.name : 'Import'"></span>
            </x-danger-button>
          </div>
        </form>
    </div>

    @endif
  </div>
  @if ($mode && $previewing)
    <h3 class="mt-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
      {{ __('Preview') . ' ' . $mode }}
    </h3>
    <div class="mt-4 w-full overflow-x-scroll text-sm">
      @php
        $trClass = 'divide-x divide-gray-200 dark:divide-gray-700';
        $thClass = 'px-4 py-3 text-left font-semibold dark:text-white';
        $tdClass = 'px-4 py-4 text-sm font-medium text-gray-900 dark:text-white';
      @endphp
      <table class="w-full divide-y divide-gray-200 border dark:divide-gray-700 dark:border-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
          <tr class="{{ $trClass }}">
            <th scope="col" class="px-2 py-3 text-left font-semibold dark:text-white">
              No
            </th>
            <th class="{{ $thClass }}">Date</th>
            <th class="{{ $thClass }}">Name</th>
            <th class="{{ $thClass }}">NIP</th>
            <th class="{{ $thClass }} text-nowrap">Time In</th>
            <th class="{{ $thClass }} text-nowrap">Time Out</th>
            <th class="{{ $thClass }}">Shift</th>
            <th class="{{ $thClass }} text-nowrap">Barcode Id</th>
            <th class="{{ $thClass }}">Coordinates</th>
            <th class="{{ $thClass }}">Status</th>
            <th class="{{ $thClass }}">Note</th>
            <th class="{{ $thClass }}">Attachment</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
          @foreach ($attendances as $attendance)
            <tr class="{{ $trClass }}">
              <td class="px-2 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">
                {{ $loop->iteration }}
              </td>
              <td class="{{ $tdClass }} text-nowrap">{{ $attendance->date?->format('Y-m-d') }}</td>
              <td class="{{ $tdClass }}">{{ $attendance->user?->name }}</td>
              <td class="{{ $tdClass }}">{{ $attendance->user?->nip }}</td>
              <td class="{{ $tdClass }}">{{ $attendance->time_in?->format('H:i:s') }}</td>
              <td class="{{ $tdClass }}">{{ $attendance->time_out?->format('H:i:s') }}</td>
              <td class="{{ $tdClass }} text-nowrap">{{ $attendance->shift?->name }}</td>
              <td class="{{ $tdClass }}">{{ $attendance->barcode_id }}</td>
              <td class="{{ $tdClass }}">
                {{ $attendance->lat_lng ? $attendance->latitude . ',' . $attendance->longitude : null }}
              </td>
              <td class="{{ $tdClass }} text-nowrap">{{ __($attendance->status) }}</td>
              <td class="{{ $tdClass }}">
                <div class="w-48">{{ Str::limit($attendance->note, 30, '...') }}</div>
              </td>
              <td class="{{ $tdClass }}">
                <img src="{{ $attendance->attachment }}" class="max-h-48 object-contain">
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
