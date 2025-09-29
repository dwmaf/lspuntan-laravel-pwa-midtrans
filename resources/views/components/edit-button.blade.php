<button {{ $attributes->merge([ 'class' => 'space-x-1 inline-flex items-center px-2 py-1 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-700 transition ease-in-out duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>
