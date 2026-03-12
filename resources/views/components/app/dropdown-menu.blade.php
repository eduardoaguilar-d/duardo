@props(['title' => null, 'align' => 'right'])

@php
$alignClass = $align === 'left' ? 'left-0' : 'right-0';
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute {{ $alignClass }} z-50 my-4 max-w-sm text-base list-none bg-white rounded-xl divide-y divide-gray-100 shadow-lg dark:bg-gray-700 dark:divide-gray-600 overflow-hidden"
         style="display: none;">
        @if($title)
            <div class="block py-2 px-4 text-base font-medium text-center text-gray-700 bg-gray-50 dark:bg-gray-600 dark:text-gray-300">
                {{ $title }}
            </div>
        @endif
        {{ $slot }}
    </div>
</div>
