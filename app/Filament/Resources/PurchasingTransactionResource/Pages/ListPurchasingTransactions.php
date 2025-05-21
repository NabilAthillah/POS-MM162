<?php

namespace App\Filament\Resources\PurchasingTransactionResource\Pages;

use App\Filament\Resources\PurchasingTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchasingTransactions extends ListRecords
{
    protected static string $resource = PurchasingTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(fn() => auth()->user()?->can('create Purchasing Transaction')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('view-any Purchasing Transaction');
    }
}
