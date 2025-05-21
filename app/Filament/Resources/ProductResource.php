<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-s-shopping-bag';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('view-any Product');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view-any Product');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('products'),
                Group::make([
                    TextInput::make('name')
                        ->required(),
                    Select::make('category')
                        ->options([
                            'makanan' => 'Makanan',
                            'minuman' => 'Minuman',
                            'bahan_pokok' => 'Bahan Pokok'
                        ])
                        ->native(false)
                        ->required(),
                    TextInput::make('purchase_price')
                        ->prefix('Rp.')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->step(1)
                        ->required(),
                    TextInput::make('selling_price')
                        ->prefix('Rp.')
                        ->mask(RawJs::make('$money($input)'))
                        ->stripCharacters(',')
                        ->numeric()
                        ->step(1)
                        ->required(),
                    TextInput::make('unit')
                        ->label('Selling Unit')
                        ->required(),
                    TextInput::make('stock')
                        ->numeric()
                        ->step(1)
                ])->columns(2)
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('products')
                    ->conversion('thumb'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('selling_price')
                    ->label('Selling Price')
                    ->formatStateUsing(function ($state, $record) {
                        return 'Rp ' . number_format($state, 0, ',', '.') . ' / ' . $record->unit;
                    }),
                TextColumn::make('stock')
                    ->formatStateUsing(function ($state, $record) {
                        return $state . ' ' . $record->unit;
                    })
            ])
            ->filters([
                Filter::make('selling_price_range')
                    ->form([
                        TextInput::make('min_price')
                            ->label('Minimum Price')
                            ->numeric()
                            ->prefix('Rp'),
                        TextInput::make('max_price')
                            ->label('Maximum Price')
                            ->numeric()
                            ->prefix('Rp'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['min_price'], fn($q) => $q->where('selling_price', '>=', $data['min_price']))
                            ->when($data['max_price'], fn($q) => $q->where('selling_price', '<=', $data['max_price']));
                    }),

                Filter::make('stock_condition')
                    ->form([
                        TextInput::make('min_stock')
                            ->label('Minimal Stock')
                            ->numeric(),
                        TextInput::make('max_stock')
                            ->label('Maximal Stock')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['min_stock'], fn($q) => $q->where('stock', '>=', $data['min_stock']))
                            ->when($data['max_stock'], fn($q) => $q->where('stock', '<=', $data['max_stock']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->can('view Product')),
                Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->can('update Product')),
                Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->can('delete Product')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->can('delete Product')),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
