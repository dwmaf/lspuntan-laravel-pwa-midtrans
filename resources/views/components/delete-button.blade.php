<button {{ $attributes->merge([ 'class' => 'inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-700 dark:hover:bg-red-800 dark:focus:ring-red-800 transition ease-in-out duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>
