<?php

namespace App\Filament\Widgets;

use App\Models\Memorize;
use Carbon\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class HafalanChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'hafalanChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Statistik Hafalan 6 Bulan Terakhir';

    /**
     * Sort Order
     */
    protected static ?int $sort = 2;

    /**
     * Chart options (series, key, labels, colors, etc.)
     */
    protected function getOptions(): array
    {
        // Get data for last 6 months
        $months = collect();
        $data = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));
            
            $count = Memorize::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $data->push($count);
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Total Hafalan',
                    'data' => $data->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $months->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#E5077C'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 5,
                    'columnWidth' => '60%',
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
        ];
    }
}
