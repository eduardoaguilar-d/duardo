@props(['href' => '#', 'icon' => null, 'badge' => null])

<a
    href="{{ $href }}"
    wire:navigate
    {{ $attributes->merge(['class' => 'flex items-center p-2 text-base font-medium text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group']) }}
>
    @if($icon)
        <span class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white">
            {!! $icon !!}
        </span>
    @endif
    <span class="{{ $icon ? 'ml-3' : '' }} flex-1 whitespace-nowrap">{{ $slot }}</span>
    @if($badge !== null)
        <span class="inline-flex justify-center items-center w-5 h-5 text-xs font-semibold rounded-full text-indigo-800 bg-indigo-100 dark:bg-indigo-200 dark:text-indigo-800">
            {{ $badge }}
        </span>
    @endif
</a>
