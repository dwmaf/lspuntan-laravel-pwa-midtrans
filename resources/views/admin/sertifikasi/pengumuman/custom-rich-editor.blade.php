{{-- Container utama dengan class khusus untuk ditemukan oleh JS --}}
<div class="rich-text-editor-wrapper block w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
    
    {{-- Toolbar dengan data-command untuk setiap tombol --}}
    <div class="toolbar flex p-3 space-x-2 dark:text-gray-400">
        <button type="button" data-command="toggleBold" class="cursor-pointer p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
            <x-fas-bold class="w-4 h-4" />
        </button>
        <button type="button" data-command="toggleItalic" class="cursor-pointer p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
            <x-fas-italic class="w-4 h-4" />
        </button>
        <button type="button" data-command="toggleUnderline" class="cursor-pointer p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
            <x-fas-underline class="w-4 h-4" />
        </button>
        <button type="button" data-command="toggleBulletList" class="cursor-pointer p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
            <x-fas-list-ul class="w-4 h-4" />
        </button>
        <button type="button" data-command="unsetAllMarks" class="cursor-pointer p-2 rounded hover:bg-gray-200 dark:hover:bg-gray-700">
            <x-fas-text-slash class="w-4 h-4" />
        </button>
    </div>
    <hr class="border-gray-300 dark:border-gray-700">

    {{-- Area editor --}}
    <div class="editor-content p-3 focus:outline-none min-h-[150px]"></div>

    {{-- Input hidden untuk menyimpan data. Kita akan mengisinya dengan JS --}}
    <input type="hidden" name="{{ $inputName }}" value="{{ $initialValue ?? '' }}">
</div>