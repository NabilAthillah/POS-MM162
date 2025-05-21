<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class TransactionChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Profit';

    protected int|string|array $columnSpan = 12;

    protected function getData(): array
    {
        $profits = collect();
        $labels = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $nextDay = $date->copy()->endOfDay();

            $transactions = Transaction::with(['products'])
                ->where('type', 'selling')
                ->whereBetween('created_at', [$date, $nextDay])
                ->get();

            $dailyProfit = 0;

            foreach ($transactions as $transaction) {
                foreach ($transaction->products as $product) {
                    $purchasePrice = $product->purchase_price;
                    $qty = $product->pivot->qty;
                    $subtotal = $product->pivot->subtotal;
                    $profit = $subtotal - ($purchasePrice * $qty);
                    $dailyProfit += $profit;
                }
            }

            $profits->push(round($dailyProfit));
            $labels->push($date->format('D'));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Profit',
                    'data' => $profits,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
