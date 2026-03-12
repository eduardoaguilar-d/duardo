@props(['drawerId' => 'drawer-navigation'])

<aside
    id="{{ $drawerId }}"
    aria-label="Sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-14 transition-transform duration-200 bg-white border-r border-gray-200 -translate-x-full md:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    :class="{ 'translate-x-0': sidebarOpen }"
>
    <div class="overflow-y-auto py-5 px-3 h-full bg-white dark:bg-gray-800">
        @if(isset($search))
            <div class="md:hidden mb-2">
                {{ $search }}
            </div>
        @endif
        <ul class="space-y-2">
            {{ $slot }}
        </ul>
        @if(isset($footer))
            <ul class="pt-5 mt-5 space-y-2 border-t border-gray-200 dark:border-gray-700">
                {{ $footer }}
            </ul>
        @endif
    </div>
    @if(isset($bottomBar))
        <div class="hidden absolute bottom-0 left-0 justify-center p-4 space-x-4 w-full lg:flex bg-white dark:bg-gray-800 z-20">
            {{ $bottomBar }}
        </div>
    @endif
</aside>
