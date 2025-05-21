<?php

namespace App\Filament\Resources\SellingTransactionResource\Pages;

use App\Filament\Resources\SellingTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSellingTransactions extends ListRecords
{
    protected static string $resource = SellingTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()->visible(fn() => auth()->user()?->can('create Product')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('view-any Selling Transaction');
    }
}
