<?php

namespace App\Filament\Resources\SellingTransactionResource\Pages;

use App\Filament\Resources\SellingTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSellingTransaction extends CreateRecord
{
    protected static string $resource = SellingTransactionResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()->can('create Selling Transaction');
    }
}
