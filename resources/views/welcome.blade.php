<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 flex flex-col items-center justify-center p-6">

        <div class="text-center mb-10">
            <h1 class="text-5xl font-black text-gray-900 mb-2">🚀 Duardo</h1>
            <p class="text-gray-500 text-lg">Laravel · Livewire · Alpine.js · Tailwind CSS</p>
        </div>

        <div class="w-full max-w-md">
            <livewire:counter />
        </div>

        <div class="mt-10 flex gap-6 text-sm text-gray-400">
            <span>✅ Laravel {{ app()->version() }}</span>
            <span>✅ Livewire 3</span>
            <span>✅ Alpine.js</span>
            <span>✅ Tailwind v4</span>
        </div>
    </div>
</x-layouts.app>
