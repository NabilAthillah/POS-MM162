<x-filament-panels::page>
    <div class="w-full h-full grid" style="grid-template-columns: 1fr auto; gap: 12px;">
        <div class="w-full grid" style="grid-template-columns: auto auto auto; gap: 12px;">
            @foreach (\App\Models\Product::all() as $produk)
                <div class="rounded-xl shadow-sm bg-white dark:bg-gray-900 flex items-center justify-between"
                    style="height: fit-content; padding-inline: 20px; padding-block: 16px;">
                    <div class="flex flex-col gap-2">
                        <h3 class="text-base font-semibold">{{ $produk->name }}</h3>
                        <p class="text-sm">Rp{{ number_format($produk->selling_price) }}</p>
                    </div>
                    <button wire:click="tambahProduk('{{ $produk->id_produk }}')"
                        class="bg-primary-500 text-white rounded-lg text-lg font-bold"
                        style="width: 28px; height: 28px;">+</button>
                </div>
            @endforeach
        </div>
        <div class="flex flex-col gap-2">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm flex flex-col gap-4"
                style="padding-inline: 20px; padding-block: 16px;">
                <h2 class="text-lg font-semibold mb-2 border-b" style="padding-right: 160px; text-align: start">Detail
                    Pesanan</h2>
                <div class="flex flex-col gap-2">
                    @foreach ($this->keranjang as $item)
                        <div class="flex flex-col gap-2 border-b" style="padding-bottom: 8px">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold">{{ $item['nama'] }}</p>
                                <p class="text-xs font-light">{{ $item['qty'] }} x Rp{{ number_format($item['subtotal']) }}
                                </p>
                            </div>
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="hapusItem('{{ $item['id_produk'] }}')" type="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="red" class="size-6"
                                        style="width: 22px; height: 22px;">
                                        <path fill-rule="evenodd"
                                            d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div class="flex items-center gap-4 border-2"
                                    style="width: fit-content; padding-inline: 12px; padding-block: 2px; border-radius: 9999px;">
                                    <button wire:click="kurangiQty('{{ $item['id_produk'] }}')"
                                        class="text-lg font-bold">-</button>
                                    <p>{{ $item['qty'] }}</p>
                                    <button wire:click="tambahQty('{{ $item['id_produk'] }}')"
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
                        <p>Rp. {{ number_format($this->totalHarga) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-filament::modal :visible="$showModalPembayaran" close-by-clicking-away wire:model.defer="showModalPembayaran">
        <x-slot name="header">
            <h2 class="text-lg font-semibold">Pilih Metode Pembayaran</h2>
        </x-slot>

        <x-slot name="footer">
            <button wire:click="$set('showModalPembayaran', false)" class="text-sm text-gray-600">Batal</button>
        </x-slot>
    </x-filament::modal>
    @push('scripts')
        <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
        <script>
            window.addEventListener('midtrans-payment', function (event) {
                snap.pay(event.detail.snapToken, {
                    onSuccess: function (result) {
                        window.livewire.emit('pembayaranBerhasil', result);
                    },
                    onPending: function (result) {
                        alert("Menunggu pembayaran...");
                    },
                    onError: function (result) {
                        alert("Pembayaran gagal!");
                    },
                    onClose: function () {
                        alert('Anda belum menyelesaikan pembayaran.');
                    }
                });
            });
        </script>
    @endpush
</x-filament-panels::page>