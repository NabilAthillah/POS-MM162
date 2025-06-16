<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\Transaction;
use Auth;
use Filament\Notifications\Notification;
use Midtrans\Config;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Midtrans\Snap;
use Str;

class PointOfSale extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-calculator';

    protected static ?string $navigationLabel = 'PoS';

    protected static string $view = 'filament.pages.point-of-sale';

    public $cart = [];

    protected $listeners = ['addProduct'];

    public function getTotalQtyProperty()
    {
        return collect($this->cart)->sum('qty');
    }

    public function getTotalPriceProperty()
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function addProduct($productId)
    {
        $product = Product::findOrFail($productId);
        $price = $product->selling_price;

        foreach ($this->cart as &$item) {
            if ($item['product_id'] === $product->id) {
                $item['qty'] += 1;
                $item['subtotal'] = $item['qty'] * $item['price'];
                return;
            }
        }

        $this->cart[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $price,
            'qty' => 1,
            'subtotal' => $price,
        ];
    }

    public function increaseQty($product_id)
    {
        foreach ($this->cart as &$item) {
            if ($item['product_id'] == $product_id) {
                $item['qty'] += 1;
                $item['subtotal'] = $item['qty'] * $item['price'];
                break;
            }
        }
    }


    public function decreaseQty($product_id)
    {
        foreach ($this->cart as $i => &$item) {
            if ($item['product_id'] == $product_id) {
                if ($item['qty'] > 1) {
                    $item['qty'] -= 1;
                    $item['subtotal'] = $item['qty'] * $item['price'];
                } else {
                    unset($this->cart[$i]);
                }
                break;
            }
        }

        $this->cart = array_values($this->cart);
    }

    public function deleteItem($product_id)
    {
        $this->cart = collect($this->cart)
            ->reject(fn($item) => $item['product_id'] == $product_id)
            ->values()
            ->toArray();
    }

    public function getActions(): array
    {
        return [
            Action::make('checkout')
                ->label('Checkout')
                ->button()
                ->color('primary')
                ->modalHeading('Choose Payment Method')
                ->modalSubmitAction(false)
                ->modalContent(view('filament.components.payment-method'))
                ->extraAttributes(['class' => 'w-full'])
                ->hidden(fn() => empty($this->cart)),
        ];
    }

    public function paymentProcess(string $type)
    {
        \Log::info('Payment process triggered', ['type' => $type]);

        if ($type === 'cash') {
            $this->dispatch('open-modal', id: 'cash-payment');
            return;
            // $this->saveTransaction('cash');
            // $this->dispatch('notify', title: 'Transaction Success');
            // $this->cart = [];
            // return;
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);

        $orderId = 'MM162-' . Str::uuid()->toString();
        $grossAmount = $this->getTotalPriceProperty();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount
            ],
            'enabled_payments' => ['other_qris']
        ];

        $snapToken = Snap::getSnapToken($params);
        \Log::info('Snap Token', ['token' => $snapToken]);

        $this->dispatch('snapTokenRecieved', token: $snapToken);
    }

    public function cashTransaction(string $type)
    {
        if ($type === 'ya') {
            $this->dispatch('close-modal', id: 'cash-payment');
            $this->dispatch('open-modal', id: 'scan');
            return;
        } else {
            $this->saveTransaction('cash');
            $this->dispatch('notify', title: 'Transaction Success');
            $this->cart = [];
            return;
        }
    }

    public function processCash()
    {
        $this->saveTransaction('cash');
        $this->dispatch('notify', title: 'Transaction Success');
        $this->cart = [];
        return;
    }

    public function closeScanModal() {
        $this->dispatch('close-modal', id: 'scan');
    }

    public function saveTransaction(string $paymentType, string $status = 'paid', ?string $orderId = null)
    {
        $transaction = Transaction::create([
            'id' => Str::uuid(),
            'type' => 'selling',
            'user_id' => Auth::id(),
            'selling_total_price' => $this->getTotalPriceProperty(),
            'fee' => 0,
            'total' => $this->getTotalPriceProperty(),
            'payment_method' => $paymentType,
        ]);

        foreach ($this->cart as $item) {
            $transaction->products()->attach($item['product_id'], [
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['subtotal'],
                'id' => Str::uuid(),
            ]);

            $product = Product::find($item['product_id']);
            $product->decrement('stock', $item['qty']);
        }

        Notification::make()
            ->title('Transaction Created Successfully')
            ->success()
            ->send();

        return redirect('admin/point-of-sale');
    }
}
