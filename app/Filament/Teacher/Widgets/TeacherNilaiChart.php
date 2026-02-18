<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Memorize;
use App\Models\MentorStudent;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TeacherNilaiChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'teacherNilaiChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Distribusi Nilai Hafalan Santri Binaan';

    /**
     * Sort Order
     */
    protected static ?int $sort = 4;

    /**
     * Widget column span
     */
    protected int | string | array $columnSpan = 1;

    /**
     * Chart options (series, key, labels, colors, etc.)
     */
    protected function getOptions(): array
    {
        $teacher = Auth::user()?->teacher;

        if (!$teacher) {
            return $this->emptyChart();
        }

        // Ambil ID santri binaan
        $studentIds = MentorStudent::where('id_teacher', $teacher->id)
            ->pluck('id_student');

        if ($studentIds->isEmpty()) {
            return $this->emptyChart();
        }

        // Hitung distribusi nilai hanya dari santri binaan
        $gradeA = Memorize::whereIn('id_student', $studentIds)->where('nilai_avg', 'A')->count();
        $gradeB = Memorize::whereIn('id_student', $studentIds)->where('nilai_avg', 'B')->count();
        $gradeC = Memorize::whereIn('id_student', $studentIds)->where('nilai_avg', 'C')->count();
        $gradeD = Memorize::whereIn('id_student', $studentIds)->where('nilai_avg', 'D')->count();

        $total = $gradeA + $gradeB + $gradeC + $gradeD;

        if ($total === 0) {
            return $this->emptyChart();
        }

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 320,
                'fontFamily' => 'inherit',
            ],
            'series' => [$gradeA, $gradeB, $gradeC, $gradeD],
            'labels' => [
                'Nilai A (' . $gradeA . ')',
                'Nilai B (' . $gradeB . ')',
                'Nilai C (' . $gradeC . ')',
                'Nilai D (' . $gradeD . ')',
            ],
            'colors' => ['#10B981', '#3B82F6', '#F59E0B', '#EF4444'],
            'legend' => [
                'position' => 'bottom',
                'fontFamily' => 'inherit',
                'fontSize' => '13px',
                'markers' => [
                    'size' => 5,
                ],
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '65%',
                        'labels' => [
                            'show' => true,
                            'name' => [
                                'show' => true,
                                'fontSize' => '14px',
                                'fontWeight' => 600,
                            ],
                            'value' => [
                                'show' => true,
                                'fontSize' => '24px',
                                'fontWeight' => 700,
                            ],
                            'total' => [
                                'show' => true,
                                'label' => 'Total',
                                'fontSize' => '13px',
                                'fontWeight' => 600,
                            ],
                        ],
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'fontSize' => '12px',
                    'fontWeight' => 600,
                ],
                'dropShadow' => [
                    'enabled' => false,
                ],
            ],
            'tooltip' => [
                'enabled' => true,
            ],
            'stroke' => [
                'width' => 2,
                'colors' => ['#fff'],
            ],
            'responsive' => [
                [
                    'breakpoint' => 480,
                    'options' => [
                        'chart' => [
                            'height' => 280,
                        ],
                        'legend' => [
                            'position' => 'bottom',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function emptyChart(): array
    {
        return [
            'chart' => [
                'type' => 'donut',
                'height' => 320,
            ],
            'series' => [1],
            'labels' => ['Belum Ada Data'],
            'colors' => ['#d1d5db'],
            'legend' => [
                'position' => 'bottom',
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
        ];
    }
}
