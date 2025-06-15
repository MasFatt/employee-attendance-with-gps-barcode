<x-app-layout>
    <x-slot name="header">
      <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
        Pengajuan Izin Baru
      </h2>
    </x-slot>

    <div class="py-12">
      <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
        <div class="rounded-xl bg-white shadow-lg dark:bg-gray-900">
          <div class="px-6 py-8 sm:p-10">

            {{-- Tombol Kembali --}}
            <div class="mb-6">
              <x-secondary-button href="{{ url()->previous() }}">
                <x-heroicon-o-chevron-left class="mr-2 h-5 w-5" />
                Kembali
              </x-secondary-button>
            </div>

            {{-- Form Pengajuan Izin --}}
            <form action="{{ route('store-leave-request') }}" method="post" enctype="multipart/form-data" class="space-y-6">
              @csrf

              {{-- Status Izin --}}
              <div>
                <x-label for="status" value="{{ __('Status') }}" />
                <x-select id="status" name="status" required class="mt-1 block w-full">
                  <option value="excused" {{ (old('status') ?? $attendance?->status) === 'excused' ? 'selected' : '' }}>
                    Izin
                  </option>
                  <option value="sick" {{ (old('status') ?? $attendance?->status) === 'sick' ? 'selected' : '' }}>
                    Sakit
                  </option>
                </x-select>
                <x-input-error for="status" class="mt-2" />
              </div>

              {{-- Tanggal Mulai & Selesai --}}
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <x-label for="from" value="Tanggal Mulai" />
                  <x-input type="date" id="from" name="from" min="{{ date('Y-m-d') }}"
                    value="{{ date('Y-m-d') }}" required class="mt-1 block w-full" />
                  <x-input-error for="from" class="mt-2" />
                </div>

                <div>
                  <x-label for="to" value="Tanggal Berakhir (Opsional)" />
                  <x-input type="date" id="to" name="to" min="{{ date('Y-m-d') }}" class="mt-1 block w-full" />
                  <x-input-error for="to" class="mt-2" />
                </div>
              </div>

              {{-- Keterangan --}}
              <div>
                <x-label for="note" value="Keterangan" />
                <x-textarea id="note" name="note" class="mt-1 block w-full" required>
                  {{ old('note') ?? $attendance?->note }}
                </x-textarea>
                <x-input-error for="note" class="mt-2" />
              </div>

              {{-- Upload File Attachment --}}
              <div x-data="{ filename: null, preview: null }">
                <x-label for="attachment" value="Lampiran (Opsional)" />
                <input type="file" id="attachment" name="attachment" class="hidden"
                  x-ref="attachment"
                  x-on:change="
                    filename = $refs.attachment.files[0].name;
                    const reader = new FileReader();
                    reader.onload = (e) => preview = e.target.result;
                    reader.readAsDataURL($refs.attachment.files[0]);
                  " />

                {{-- Preview Baru --}}
                <div class="mt-3" x-show="preview">
                  <img :src="preview" class="rounded-md w-full max-h-72 object-contain" />
                </div>

                {{-- Preview dari database --}}
                @if ($attendance?->attachment)
                  <div class="mt-3" x-show="!preview">
                    <img src="{{ $attendance->attachment_url }}" class="rounded-md w-full max-h-72 object-contain" />
                  </div>
                @endif

                <div class="mt-4 flex gap-3">
                  <x-secondary-button type="button" x-on:click.prevent="$refs.attachment.click()">
                    Pilih Lampiran
                  </x-secondary-button>
                  <x-secondary-button type="button" x-show="preview" x-on:click="preview = null; filename = null">
                    Hapus
                  </x-secondary-button>
                </div>

                <x-input-error for="attachment" class="mt-2" />
              </div>

              {{-- Hidden Location Fields --}}
              <input type="hidden" id="lat" name="lat" value="{{ $attendance?->latitude }}">
              <input type="hidden" id="lng" name="lng" value="{{ $attendance?->longitude }}">

              {{-- Tombol Simpan --}}
              <div class="pt-6 flex justify-end">
                <x-button>
                  {{ __('Simpan') }}
                </x-button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>

    {{-- Geolocation Script --}}
    @pushOnce('scripts')
      <script>
        getLocation();
        async function getLocation() {
          if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
              (position) => {
                document.getElementById('lat').value = position.coords.latitude;
                document.getElementById('lng').value = position.coords.longitude;
              },
              (err) => {
                alert('{{ __('Silakan Aktifkan Lokasi Anda') }}');
              }
            );
          }
        }
      </script>
    @endPushOnce
  </x-app-layout>
