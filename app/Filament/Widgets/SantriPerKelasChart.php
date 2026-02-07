<?php

namespace App\Filament\Widgets;

use App\Models\Classes;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class SantriPerKelasChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'santriPerKelasChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Distribusi Santri Per Kelas';

    /**
     * Sort Order
     */
    protected static ?int $sort = 3;

    /**
     * Chart options (series, key, labels, colors, etc.)
     */
    protected function getOptions(): array
    {
        $classes = Classes::withCount('students')->get();
        
        $labels = $classes->pluck('class_name')->toArray();
        $data = $classes->pluck('students_count')->toArray();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => $data,
            'labels' => $labels,
            'colors' => ['#E5077C', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EF4444', '#06B6D4', '#84CC16'],
            'legend' => [
                'position' => 'bottom',
                'fontFamily' => 'inherit',
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '65%',
                        'labels' => [
                            'show' => true,
                            'total' => [
                                'show' => true,
                                'label' => 'Total Santri',
                                'fontFamily' => 'inherit',
                            ],
                        ],
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
            ],
        ];
    }
}
