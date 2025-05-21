<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = '12';

    protected function getStats(): array
    {
        $todayTransactions = Transaction::with('products')
            ->whereDate('created_at', now())
            ->where('type', 'selling')
            ->get();

        $omsetHariIni = $todayTransactions->sum('total');
        $jumlahTransaksi = $todayTransactions->count();

        $profitHariIni = 0;

        foreach ($todayTransactions as $transaction) {
            foreach ($transaction->products as $product) {
                $pivot = $product->pivot;
                $profitHariIni += $pivot->subtotal - ($pivot->qty * $product->purchase_price);
            }
        }

        return [
            Stat::make('Omset Hari Ini', 'Rp ' . number_format($omsetHariIni, 0, ',', '.'))
                ->description('Total omset dari penjualan hari ini')
                ->color('primary'),

            Stat::make('Profit Hari Ini', 'Rp ' . number_format($profitHariIni, 0, ',', '.'))
                ->description('Total profit dari penjualan hari ini')
                ->color('success'),

            Stat::make('Jumlah Transaksi', $jumlahTransaksi)
                ->description('Total transaksi hari ini')
                ->color('info'),
        ];
    }
}
