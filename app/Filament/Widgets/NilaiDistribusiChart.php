<?php

namespace App\Filament\Widgets;

use App\Models\Memorize;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class NilaiDistribusiChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'nilaiDistribusiChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Distribusi Nilai Hafalan';

    /**
     * Sort Order
     */
    protected static ?int $sort = 4;

    /**
     * Chart options (series, key, labels, colors, etc.)
     */
    protected function getOptions(): array
    {
        // Count each grade
        $gradeA = Memorize::where('nilai_avg', 'A')->count();
        $gradeB = Memorize::where('nilai_avg', 'B')->count();
        $gradeC = Memorize::where('nilai_avg', 'C')->count();
        $gradeD = Memorize::where('nilai_avg', 'D')->count();

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
                    'name' => 'Jumlah Hafalan',
                    'data' => [$gradeA, $gradeB, $gradeC, $gradeD],
                ],
            ],
            'xaxis' => [
                'categories' => ['Nilai A', 'Nilai B', 'Nilai C', 'Nilai D'],
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
            'colors' => ['#10B981', '#3B82F6', '#F59E0B', '#EF4444'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 5,
                    'columnWidth' => '50%',
                    'distributed' => true,
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
            'legend' => [
                'show' => false,
            ],
        ];
    }
}
