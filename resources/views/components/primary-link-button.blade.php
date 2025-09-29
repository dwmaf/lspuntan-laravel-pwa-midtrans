<a {{ $attributes->merge(['class' => 'cursor-pointer uppercase px-4 py-2 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-md tracking-widest active:ring-2 active:ring-offset-2 active:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 dark:active:ring-blue-700']) }}>
    {{ $slot }}
</a>