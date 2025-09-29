<button {{ $attributes->merge([ 'class' => 'space-x-2 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-700 transition ease-in-out duration-150 cursor-pointer']) }}>
    <x-bxs-plus-square class="w-4 h-4" />
    {{ $slot }}
</button>
