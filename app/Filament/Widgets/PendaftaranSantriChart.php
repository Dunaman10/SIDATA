<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Carbon\Carbon;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PendaftaranSantriChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'pendaftaranSantriChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Pendaftaran Santri 12 Bulan Terakhir';

    /**
     * Sort Order
     */
    protected static ?int $sort = 5;

    /**
     * Chart options (series, key, labels, colors, etc.)
     */
    protected function getOptions(): array
    {
        // Get data for last 12 months
        $months = collect();
        $data = collect();

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months->push($date->translatedFormat('M Y'));
            
            $count = Student::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $data->push($count);
        }

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'Santri Baru',
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
            'colors' => ['#3B82F6'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.2,
                ],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 3,
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'markers' => [
                'size' => 4,
                'hover' => [
                    'size' => 6,
                ],
            ],
        ];
    }
}
