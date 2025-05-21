<?php

namespace App\Filament\Resources\PurchasingTransactionResource\Pages;

use App\Filament\Resources\PurchasingTransactionResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchasingTransaction extends CreateRecord
{
    protected static string $resource = PurchasingTransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('create Purchasing Transaction');
    }

    protected function afterCreate(): void
    {
        $data = $this->record;

        if ($data->type === 'purchasing') {
            $product = Product::find($data->product_id);

            if ($product) {
                $additionalStock = $data->amount_per_unit * $data->amount;
                $product->stock += $additionalStock;
                $product->save();
            }
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $cleanedPrice = (float) preg_replace('/[^\d]/', '', $data['price_per_unit']);
        $amount = (float) $data['amount'];

        $data['total_price'] = $cleanedPrice * $amount;

        $data['price_per_unit'] = (int) str_replace([',', '.'], '', $data['price_per_unit']);

        return $data;
    }
}
