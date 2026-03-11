<div class="flex flex-col items-center gap-4 p-8 bg-white rounded-2xl shadow-lg">
    <h2 class="text-2xl font-bold text-gray-800">🔢 Livewire Counter</h2>

    <div class="text-6xl font-black text-indigo-600">{{ $count }}</div>

    <div class="flex gap-3">
        <button
            wire:click="decrement"
            class="px-6 py-2 bg-red-100 text-red-700 font-semibold rounded-lg hover:bg-red-200 transition"
        >−</button>

        <button
            wire:click="increment"
            class="px-6 py-2 bg-green-100 text-green-700 font-semibold rounded-lg hover:bg-green-200 transition"
        >+</button>
    </div>

    {{-- Alpine.js demo --}}
    <div x-data="{ open: false }" class="mt-4 w-full text-center">
        <button
            @click="open = !open"
            class="text-sm text-gray-500 underline hover:text-gray-700 transition"
        >Toggle Alpine.js message</button>

        <p x-show="open" x-cloak class="mt-2 text-indigo-500 font-medium">
            ✅ Alpine.js está funcionando correctamente.
        </p>
    </div>
</div>
