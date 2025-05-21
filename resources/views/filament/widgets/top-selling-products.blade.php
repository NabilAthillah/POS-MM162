<x-filament-widgets::widget>
    <x-filament::section>
        <x-filament::card>
            <h2 class="text-lg font-bold mb-4">Top 5 Produk Terlaris Minggu Ini</h2>
            <ul class="space-y-2">
                @forelse ($topProducts as $product)
                    <li class="flex justify-between border-b pb-1">
                        <span>{{ $product->name }}</span>
                        <span class="font-semibold">{{ $product->total_qty }} terjual</span>
                    </li>
                @empty
                    <li class="text-gray-500">Tidak ada data</li>
                @endforelse
            </ul>
        </x-filament::card>
    </x-filament::section>
</x-filament-widgets::widget>