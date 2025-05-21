<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchasingTransactionResource\Pages;
use App\Filament\Resources\PurchasingTransactionResource\RelationManagers;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Auth;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchasingTransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-s-shopping-cart';

    protected static ?string $navigationLabel = 'Purchasing Transactions';

    protected static ?string $navigationGroup = 'Transactions';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('view-any Purchasing Transaction');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view-any Purchasing Transaction');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'purchasing');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('type')
                    ->options([
                        'purchasing' => 'Purchasing',
                        'selling' => 'Selling'
                    ])
                    ->default('purchasing')
                    ->native(false)
                    ->disabled()
                    ->dehydrated(true),
                Select::make('user_id')
                    ->label('User')
                    ->options(User::all()->pluck('name', 'id'))
                    ->default(Auth::id())
                    ->disabled()
                    ->native(false)
                    ->dehydrated(true),
                Select::make('product_id')
                    ->label('Product')
                    ->options(Product::all()->pluck('name', 'id'))
                    ->searchable()
                    ->native(false)
                    ->required(),
                Group::make([
                    TextInput::make('unit')
                        ->required(),
                    TextInput::make('amount_per_unit')
                        ->required(),
                    TextInput::make('amount')
                        ->required(),
                    TextInput::make('price_per_unit')
                        ->prefix('Rp.')
                        ->mask(RawJs::make('$money($input)'))
                        ->required(),
                ])->columns(2),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Transaksi')
                    ->formatStateUsing(function ($state, $record) {
                        return 'Pembelian ' . $record->amount . ' ' . $record->unit . ' ' . $state . ' oleh ' . $record->user->name;
                    }),
                TextColumn::make('price_per_unit')
                    ->label('Price Per Unit')
                    ->formatStateUsing(function ($state, $record) {
                        return 'Rp ' . number_format($state, 0, ',', '.') . ' / ' . $record->unit;
                    }),
                TextColumn::make('total_price')
                    ->label('Total Price')
                    ->formatStateUsing(function ($state, $record) {
                        return 'Rp ' . number_format($state, 0, ',', '.');
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->can('view Purchasing Transaction')),
                Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->can('update Purchasing Transaction')),
                Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->can('delete Purchasing Transaction')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->can('delete Purchasing Transaction')),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchasingTransactions::route('/'),
            'create' => Pages\CreatePurchasingTransaction::route('/create'),
            'edit' => Pages\EditPurchasingTransaction::route('/{record}/edit'),
        ];
    }
}
