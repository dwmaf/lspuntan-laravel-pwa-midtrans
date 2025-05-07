<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard Asesi') }}
        </h2>
    </x-slot>
    <div class="flex flex-col md:flex-row gap-4 md:gap-2">
        <div class="flex flex-row md:flex-col items-center flex-1 gap-6">
          <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center">
            <!-- Check icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h4 class="text-sm">Daftar</h4>
        </div>
        
        <!-- Garis pemisah -->
        <div class="border-l-2 border-gray-300 h-16 md:h-full"></div>
      
        <div class="flex flex-row md:flex-col items-center flex-1 gap-6">
          <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center">
            <div class="w-3 h-3 bg-white rounded-sm"></div>
          </div>
          <h4 class="text-sm">Divalidasi Admin</h4>
        </div>
        
        <!-- Garis pemisah -->
        <div class="border-l-2 border-gray-300 h-16 md:h-full"></div>
      
        <div class="flex flex-row md:flex-col items-center flex-1 gap-6">
          <div class="w-8 h-8 border border-gray-300 rounded-xl flex items-center justify-center">
            <div class="w-3 h-3 bg-gray-300 rounded-sm "></div>
          </div>
          <h4 class="text-sm">Pra-asesmen</h4>
        </div>
      
        <!-- Garis pemisah -->
        <div class="border-l-2 border-gray-300 h-16 md:h-full"></div>
      
        <div class="flex flex-row md:flex-col items-center flex-1 gap-6">
          <div class="w-8 h-8 border border-gray-300 rounded-xl flex items-center justify-center">
            <div class="w-3 h-3 bg-gray-300 rounded-sm "></div>
          </div>
          <h4 class="text-sm">Bayar Sertifikasi</h4>
        </div>
      
        <!-- Garis pemisah -->
        <div class="border-l-2 border-gray-300 h-16 md:h-full"></div>
      
        <div class="flex flex-row md:flex-col items-center flex-1 gap-6">
          <div class="w-8 h-8 border border-gray-300 rounded-xl flex items-center justify-center">
            <div class="w-3 h-3 bg-gray-300 rounded-sm "></div>
          </div>
          <h4 class="text-sm">Asesmen</h4>
        </div>
      </div>
      
    {{-- <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xs sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div> --}}
</x-app-layout>
