<?php

namespace App\Filament\Resources\PurchasingTransactionResource\Pages;

use App\Filament\Resources\PurchasingTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchasingTransaction extends EditRecord
{
    protected static string $resource = PurchasingTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->can('delete Purchasing Transaction')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('update Purchasing Transaction');
    }
}
