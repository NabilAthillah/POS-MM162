<?php

namespace App\Filament\Pages;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Pages\Page;

class POS extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-calculator';

    protected static ?string $navigationLabel = 'PoS';

    protected static string $view = 'filament.pages.p-o-s';

    public $cart = [];

    protected $listeners = ['addProduct'];
    public bool $showPaymentModal = false;

    public function addProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $price = $product->selling_price ?? 0;

        foreach ($this->cart as &$item) {
            if ($item['product_id'] === $product->id) {
                $item['qty'] += 1;
                $item['subtotal'] = $item['qty'] * $item['harga'];
                return;
            }
        }

        $this->keranjang[] = [
            'id_produk' => $product->id,
            'nama' => $product->name,
            'harga' => $price,
            'qty' => 1,
            'subtotal' => $price,
        ];
    }

    public function hapusItem($id_produk)
    {
        $this->keranjang = collect($this->keranjang)
            ->reject(fn($item) => $item['id_produk'] == $id_produk)
            ->values()
            ->toArray();
    }

    public function tambahQty($id_produk)
    {
        foreach ($this->keranjang as &$item) {
            if ($item['id_produk'] == $id_produk) {
                $item['qty'] += 1;
                $item['subtotal'] = $item['qty'] * $item['harga'];
                break;
            }
        }
    }

    public function kurangiQty($id_produk)
    {
        foreach ($this->keranjang as $i => &$item) {
            if ($item['id_produk'] == $id_produk) {
                if ($item['qty'] > 1) {
                    $item['qty'] -= 1;
                    $item['subtotal'] = $item['qty'] * $item['harga'];
                } else {
                    unset($this->keranjang[$i]);
                }
                break;
            }
        }

        $this->keranjang = array_values($this->keranjang);
    }

    public function getTotalQtyProperty()
    {
        return collect($this->keranjang)->sum('qty');
    }

    public function getTotalHargaProperty()
    {
        return collect($this->keranjang)->sum('subtotal');
    }

    public function getActions(): array
    {
        return [
            Action::make('bayar')
                ->label('Bayar')
                ->button()
                ->color('primary')
                ->modalHeading('Pilih Metode Pembayaran')
                ->modalSubmitAction(false)
                ->modalContent(view('filament.components.metode-pembayaran'))
                ->extraAttributes(['class' => 'w-full'])
                ->hidden(fn() => empty($this->keranjang)),
        ];
    }

    public function prosesPembayaran($metode)
    {
        MidtransConfig::init();

        if ($metode !== 'cash') {
            $payload = [
                'transaction_details' => [
                    'order_id' => 'ORDER-' . now()->timestamp,
                    'gross_amount' => $this->totalHarga,
                ],
                'customer_details' => [
                    'first_name' => 'MM162',
                    'email' => 'superadmin@mm162.com',
                ],
            ];

            $snapToken = Snap::getSnapToken($payload);
            $this->dispatch('midtrans-payment', snapToken: $snapToken);
        } else {
            $this->keranjang = [];
            // $this->dispatch('notify', ['message' => 'Pembayaran berhasil dengan metode CASH']);
        }
    }
}
