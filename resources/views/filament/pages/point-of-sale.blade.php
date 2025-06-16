<x-filament-panels::page>
    <div class="w-full h-full grid" style="grid-template-columns: 1fr auto; gap: 12px;">
        <div class="w-full grid" style="grid-template-columns: auto auto auto; gap: 12px;">
            @foreach (\App\Models\Product::all() as $item)
                <div class="rounded-xl shadow-sm bg-white dark:bg-gray-900 flex flex-col" style="max-width: 320px;">
                    <img src="{{ $item->getFirstMediaUrl('products', 'preview') }}"
                        style="border-radius: 12px 12px 0px 0px;" alt="">
                    <div class="flex items-center justify-between"
                        style="height: fit-content; padding-inline: 20px; padding-block: 16px;">
                        <div class="flex flex-col gap-2">
                            <h3 class="text-base font-semibold">{{ $item->name }}</h3>
                            <p class="text-sm">Rp{{ number_format($item->selling_price) }}</p>
                            <p class="text-xs">Stock : {{ $item->stock }}</p>
                        </div>
                        <button wire:click="addProduct('{{ $item->id }}')"
                            class="bg-primary-500 text-white rounded-lg text-lg font-bold"
                            style="width: 28px; height: 28px;">+</button>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="flex flex-col gap-2">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm flex flex-col gap-4"
                style="padding-inline: 20px; padding-block: 16px;">
                <h2 class="text-lg font-semibold mb-2 border-b" style="padding-right: 160px; text-align: start">Detail
                    Pesanan</h2>
                <div class="flex flex-col gap-2">
                    @foreach ($this->cart as $item)
                        <div class="flex flex-col gap-2 border-b" style="padding-bottom: 8px">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold">{{ $item['name'] }}</p>
                                <p class="text-xs font-light">{{ $item['qty'] }} x Rp{{ number_format($item['price']) }}
                                </p>
                            </div>
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="deleteItem('{{ $item['product_id'] }}')" type="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="red" class="size-6"
                                        style="width: 22px; height: 22px;">
                                        <path fill-rule="evenodd"
                                            d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div class="flex items-center gap-4 border-2"
                                    style="width: fit-content; padding-inline: 12px; padding-block: 2px; border-radius: 9999px;">
                                    <button wire:click="decreaseQty('{{ $item['product_id'] }}')"
                                        class="text-lg font-bold">-</button>
                                    <p>{{ $item['qty'] }}</p>
                                    <button wire:click="increaseQty('{{ $item['product_id'] }}')"
                                        class="text-lg font-bold">+</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm flex flex-col gap-4"
                style="padding-inline: 20px; padding-block: 16px;">
                <h2 class="text-lg font-semibold mb-2 border-b" style="padding-right: 160px; text-align: start">Total
                </h2>
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <p>Total Produk : </p>
                        <p>{{ $this->totalQty }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <p>Subtotal : </p>
                        <p>Rp. {{ number_format($this->totalPrice) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-filament::modal id="cash-payment" :close-button="true" :close-by-escaping="true" :close-by-clicking-away="true">
        <x-slot name="heading">
            Apakah anda ingin mengecek keaslian uang?
        </x-slot>
        <x-filament::button color="success" wire:click="cashTransaction('ya')">
            Ya
        </x-filament::button>
        <x-filament::button color="danger" wire:click="cashTransaction('tidak')">
            Tidak
        </x-filament::button>
    </x-filament::modal>

    <x-filament::modal id="scan" :close-button="true" :close-by-escaping="true" :close-by-clicking-away="true">
        <x-slot name="heading">
            Input gambar uangnya
        </x-slot>
        <div>
            <input type="file" name="image" id="image" accept="image/*">
            <div id="result" class="mt-4 text-sm text-gray-700"></div>
        </div>
        <x-filament::button color="success" wire:click="processCash()">
            Lanjutkan
        </x-filament::button>
        <x-filament::button color="gray" wire:click="closeScanModal()">
            Cancel
        </x-filament::button>
    </x-filament::modal>

    @push('scripts')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script>
            window.addEventListener('snapTokenRecieved', function (event) {
                snap.pay(event.detail.token, {
                    onSuccess: function (result) {
                        Livewire.find('{{ $this->getId() }}').call('saveTransaction', 'other_qris');
                    },
                    onPending: function (result) {
                        window.Livewire.dispatch('notify', { title: 'Pembayaran Pending' });
                    },
                    onError: function (result) {
                        window.Livewire.dispatch('notify', { title: 'Pembayaran Gagal' });
                    },
                    onClose: function () {
                        alert('Kamu menutup modal tanpa menyelesaikan pembayaran.');
                    }
                })
            });

            document.addEventListener('DOMContentLoaded', function () {
                const input = document.getElementById('image');
                const resultDiv = document.getElementById('result');

                input.addEventListener('change', async function () {
                    const file = input.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('file', file);

                    resultDiv.innerText = 'Memproses...';

                    try {
                        const response = await fetch('http://localhost:8080/predict', {
                            method: 'POST',
                            body: formData
                        });

                        if (response.status != 200) {
                            throw new Error('Server error');
                        }

                        const data = await response.json();
                        resultDiv.innerHTML = `
                                    <strong>Hasil:</strong> ${data.label}<br>
                                `;
                    } catch (error) {
                        console.error(error);
                        resultDiv.innerText = 'Terjadi kesalahan saat mengirim gambar.';
                    }
                });
            });
        </script>
    @endpush
</x-filament-panels::page>