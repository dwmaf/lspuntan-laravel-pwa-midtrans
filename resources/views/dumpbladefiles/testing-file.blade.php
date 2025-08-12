{{-- utk nanganin dropdown checkboxnya, we use alpine js  --}}
{{-- <form action="/admin/asesor" class="mt-4 flex flex-col gap-2" method="POST" x-data="{
                selectedSkemas: [], // Ini akan menyimpan ID skema yang dipilih
                open: false, // mengontrol visibilitas dropdown
                toggleDropdown() {
                    this.open = !this.open; //membalik nilai open
                },
                // fungsi ini akan terpanggil jika pengguna mengklik checkbox
                toggleSkema(skemaId) {
                    // cek apakah skemaId sudah ada di array
                    if (this.selectedSkemas.includes(skemaId)) {
                        // kalau ada, ini akan buat array baru tanpa si skemaId yg dibawa, soalnya kalau ada, berarti si user menghilangkan centang
                        this.selectedSkemas = this.selectedSkemas.filter(id => id !== skemaId);
                    } else {
                        // jika belum ada, tambahkan skemaId ke array
                        this.selectedSkemas.push(skemaId);
                    }

                },
                isSelected(skemaId) {
                    // return true jika skemaId ada di array
                    return this.selectedSkemas.includes(skemaId);
                },
                
            }">
            @csrf
            <div>
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Nama
                    Asesor</label>
                <x-text-input name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
            </div>
            <div>
                <label for="" class="block text-sm font-medium text-gray-600 dark:text-gray-300">Email</label>
                <x-text-input name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
            </div>

            <div>
                <label for=""
                    class="block text-sm font-medium text-gray-600 dark:text-gray-300">Password</label>
                <x-text-input required name="password" type="password" class="mt-1 block w-full" :value="old('password')" />

            </div>
            <!-- <input type="text" name="role" value="asesor" hidden>-->
            <div  class="relative mt-2">
                <button type="button" @click="toggleDropdown"
                    class="p-2 text-sm font-medium rounded-t-md w-full text-left flex justify-between items-center mt-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white ">
                    <span>
                        Pilih Skema
                        <span x-show="selectedSkemas.length > 0" x-text="`(${selectedSkemas.length} selected)`"
                            class="ml-1 text-xs text-gray-400 "></span>
                        <span x-show="selectedSkemas.length === 0"
                            class="ml-1 text-xs text-red-400 ">Pilih minimal 1 skema</span>
                    </span>
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 transform transition-transform duration-200"
                        :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div class="absolute left-0 w-full rounded-b-md z-20 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-white "
                    x-show="open" @click.away="open = false" x-cloak>
                    <div class="p-2 max-h-60 overflow-y-auto">
                        @foreach ($skemas as $skema)
                            <div class="flex items-center p-1 hover:bg-gray-300  dark:hover:bg-gray-700 rounded">
                                <input type="checkbox" id="skema_{{ $skema->id }}" value="{{ $skema->id }}"
                                    @click="toggleSkema({{ $skema->id }})" :checked="isSelected({{ $skema->id }})"
                                    name="selectedSkemas[]" class="mr-2 rounded text-blue-500 focus:ring-blue-400">
                                <label for="skema_{{ $skema->id }}"
                                    class="text-sm  cursor-pointer flex-grow">{{ $skema->nama_skema }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <button type="submit" 
                :disabled="selectedSkemas.length === 0"
                class="self-start font-medium bg-blue-500 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">Tambah</button>
        </form> --}}

