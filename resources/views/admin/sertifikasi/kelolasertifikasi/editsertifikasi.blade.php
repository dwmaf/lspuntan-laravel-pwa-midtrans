<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Sertifikasi') }}
        </h2>
    </x-slot>
    <div x-data="{ hargaValue: '{{ old('harga', $sertification->harga) }}' }" class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">

        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Edit Sertifikasi</h2>
        <form action="{{ route('admin.kelolasertifikasi.update', $sertification->id) }}" class="mt-4 flex flex-col gap-2"
            method="POST">
            @csrf
            @method('PATCH')
            <div id="asesor dan skema">
                <label for="skema_asesor" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pilih Skema
                    dan Asesor:</label>
                <select required name="asesor_skema"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="" disabled>--Silahkan pilih asesor dan skema--</option>
                    @foreach ($asesors as $asesor)
                        @foreach ($asesor->skemas as $skema)
                            <option value="{{ $asesor->id . ',' . $skema->id }}"
                                @if (old('asesor_skema', $sertification->asesor_id . ',' . $sertification->skema_id) == $asesor->id . ',' . $skema->id) selected @endif>
                                {{ $asesor->user->name }} - {{ $skema->nama_skema }}
                            </option>
                        @endforeach
                    @endforeach
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('asesor_skema')" />
            </div>
            <div id="tanggal_apply_dibuka">
                <label for="tgl_apply_dibuka" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal
                    Daftar
                    Dibuka
                </label>
                <x-text-input id="tgl_apply_dibuka" name="tgl_apply_dibuka" type="date" class="mt-1 block w-full"
                    :value="old('tgl_apply_dibuka', $sertification->tgl_apply_dibuka)" required />
                <x-input-error class="mt-2" :messages="$errors->get('tgl_apply_dibuka')" />
            </div>
            <div id="tanggal_apply_ditutup">
                <label for="tgl_apply_ditutup"
                    class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal Daftar
                    Ditutup
                </label>
                <x-text-input id="tgl_apply_ditutup" name="tgl_apply_ditutup" type="date" class="mt-1 block w-full"
                    :value="old('tgl_apply_ditutup', $sertification->tgl_apply_ditutup)" required />
                <x-input-error class="mt-2" :messages="$errors->get('tgl_apply_ditutup')" />
            </div>
            <div id="tanggal_bayar_ditutup">
                <label for="tgl_bayar_ditutup"
                    class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tanggal Bayar
                    Ditutup
                </label>
                <x-text-input id="tgl_bayar_ditutup" name="tgl_bayar_ditutup" type="datetime-local"
                    class="mt-1 block w-full" :value="old('tgl_bayar_ditutup', $sertification->tgl_bayar_ditutup)" required />
                <x-input-error class="mt-2" :messages="$errors->get('tgl_bayar_ditutup')" />
            </div>
            <div id="biaya_sertifikasi">
                <label for="harga" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Biaya
                    Sertifikasi
                </label>
                <p x-show="hargaValue" class="text-sm font-medium text-gray-800 dark:text-gray-200 mt-1"
                    style="display: none;">
                    <span
                        x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(hargaValue)"></span>
                </p>
                <x-text-input id="harga" name="harga" type="number" min="0" class="mt-1 block w-full"
                    :value="old('harga', $sertification->harga)" x-model="hargaValue" required />
                <x-input-error class="mt-2" :messages="$errors->get('harga')" />
            </div>
            <div id="tuk">
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Tempat Uji
                    Sertifikasi
                </label>
                <x-text-input id="tuk" name="tuk" type="text" class="mt-1 block w-full" :value="old('tuk', $sertification->tuk)"
                    required />
                <x-input-error class="mt-2" :messages="$errors->get('tuk')" />
            </div>
            <div id="status" class="mb-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <div class="flex gap-4">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="status" value="berlangsung"
                            class="cursor-pointer dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                            {{ old('status', $sertification->status) == 'berlangsung' ? 'checked' : '' }} required>
                        <span class="ml-2 dark:text-gray-300">Sedang Berlangsung</span>
                    </label>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="radio" name="status" value="selesai"
                            class="cursor-pointer dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                            {{ old('status', $sertification->status) == 'selesai' ? 'checked' : '' }} required>
                        <span class="ml-2 dark:text-gray-300">Selesai</span>
                    </label>
                </div>
                <x-input-error class="mt-2" :messages="$errors->get('status')" />
            </div>
            <button type="submit"
                class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer">Update</button>
        </form>
    </div>
</x-admin-layout>
