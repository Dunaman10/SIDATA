<?php

namespace App\Filament\Teacher\Widgets;

use App\Models\Memorize;
use App\Models\MentorStudent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TeacherHafalanChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'teacherHafalanChart';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Progres Hafalan Santri Binaan (6 Bulan Terakhir)';

    /**
     * Sort Order
     */
    protected static ?int $sort = 3;

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

        // Ambil santri binaan
        $mentored = MentorStudent::where('id_teacher', $teacher->id)
            ->with('student')
            ->get();

        $studentIds = $mentored->pluck('id_student')->toArray();
        $studentNames = $mentored->pluck('student.student_name', 'id_student')->toArray();

        // Warna untuk setiap santri
        $colors = ['#E5077C', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6'];

        // Generate 6 bulan labels
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i)->translatedFormat('M Y'));
        }

        // Build series per santri
        $series = [];
        $colorIndex = 0;

        foreach ($studentIds as $studentId) {
            $data = collect();
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $count = Memorize::where('id_student', $studentId)
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
                $data->push($count);
            }

            $series[] = [
                'name' => $studentNames[$studentId] ?? 'Santri #' . $studentId,
                'data' => $data->toArray(),
            ];
            $colorIndex++;
        }

        if (empty($series)) {
            return $this->emptyChart();
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 320,
                'toolbar' => [
                    'show' => false,
                ],
                'fontFamily' => 'inherit',
            ],
            'series' => $series,
            'xaxis' => [
                'categories' => $months->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                        'fontWeight' => 500,
                    ],
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Jumlah Setoran',
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => array_slice($colors, 0, count($series)),
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 4,
                    'columnWidth' => '65%',
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'fontSize' => '11px',
                    'fontWeight' => 600,
                ],
            ],
            'legend' => [
                'position' => 'bottom',
                'fontFamily' => 'inherit',
                'fontSize' => '13px',
                'markers' => [
                    'size' => 5,
                ],
            ],
            'tooltip' => [
                'shared' => true,
                'intersect' => false,
            ],
            'grid' => [
                'borderColor' => '#f1f1f1',
                'strokeDashArray' => 4,
            ],
        ];
    }

    private function emptyChart(): array
    {
        return [
            'chart' => [
                'type' => 'bar',
                'height' => 320,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                ['name' => 'Belum ada data', 'data' => [0, 0, 0, 0, 0, 0]],
            ],
            'xaxis' => [
                'categories' => collect(range(5, 0))->map(fn($i) => Carbon::now()->subMonths($i)->translatedFormat('M Y'))->toArray(),
            ],
            'colors' => ['#d1d5db'],
        ];
    }
}
