<?php

namespace App\Filament\Resources\SellingTransactionResource\Pages;

use App\Filament\Resources\SellingTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSellingTransaction extends EditRecord
{
    protected static string $resource = SellingTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->visible(fn () => auth()->user()?->can('delete Selling Transaction')),
        ];
    }

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('update Selling Transaction');
    }
}
