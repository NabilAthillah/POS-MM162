<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SellingTransactionResource\Pages;
use App\Filament\Resources\SellingTransactionResource\RelationManagers;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SellingTransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-s-banknotes';

    protected static ?string $navigationLabel = 'Selling Transactions';

    protected static ?string $navigationGroup = 'Transactions';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->can('view-any Selling Transaction');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view-any Selling Transaction');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('type', 'selling')->orderBy('created_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y H:i')
                    ->timezone('Asia/Jakarta'),
                TextColumn::make('products')
                    ->label('Products')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->products->map(function ($product) {
                            return "{$product->name} (x{$product->pivot->qty}) - Rp" . number_format($product->pivot->subtotal);
                        })->implode(', ');
                    })
                    ->wrap(),
            ])
            ->filters([
                Filter::make('Hari Ini')
                    ->query(fn(Builder $query) => $query->whereDate('created_at', Carbon::today())),

                Filter::make('1 Minggu Terakhir')
                    ->query(fn(Builder $query) => $query->where('created_at', '>=', now()->subWeek())),

                Filter::make('1 Bulan Terakhir')
                    ->query(fn(Builder $query) => $query->where('created_at', '>=', now()->subMonth())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->visible(fn() => auth()->user()?->can('view Selling Transaction')),
                Tables\Actions\EditAction::make()->visible(fn() => auth()->user()?->can('update Selling Transaction')),
                Tables\Actions\DeleteAction::make()->visible(fn() => auth()->user()?->can('delete Selling Transaction')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->visible(fn() => auth()->user()?->can('delete Selling Transaction')),
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
            'index' => Pages\ListSellingTransactions::route('/'),
            'create' => Pages\CreateSellingTransaction::route('/create'),
            'edit' => Pages\EditSellingTransaction::route('/{record}/edit'),
        ];
    }
}
