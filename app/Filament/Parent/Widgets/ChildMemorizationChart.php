<?php

namespace App\Filament\Parent\Widgets;

use App\Models\Student;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class ChildMemorizationChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Perkembangan Hafalan Mingguan';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Ambil anak-anak dari user yang login
        $children = Student::where('parent', $user->id)->get();
        
        $datasets = [];
        $labels = [];
        
        // Setup Labels (8 Pekan Terakhir)
        for ($i = 7; $i >= 0; $i--) {
            $startOfWeek = now()->subWeeks($i)->startOfWeek();
            $endOfWeek = now()->subWeeks($i)->endOfWeek();
            // Format label contoh: "01 Jan - 07 Jan"
            $labels[] = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M');
        }

        $colors = [
            '#E5077C', // Primary Brand Color
            '#36A2EB', // Blue
            '#FFCE56', // Yellow
            '#4BC0C0', // Teal
            '#9966FF', // Purple
        ];

        foreach ($children as $index => $child) {
            $data = [];
            for ($i = 7; $i >= 0; $i--) {
                 $startOfWeek = now()->subWeeks($i)->startOfWeek();
                 $endOfWeek = now()->subWeeks($i)->endOfWeek();
                 
                 $count = $child->memorizes()
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->count();
                    
                 $data[] = $count;
            }

            $color = $colors[$index % count($colors)];

            $datasets[] = [
                'label' => $child->student_name,
                'data' => $data,
                'borderColor' => $color,
                'backgroundColor' => $color,
                'fill' => false,
                'tension' => 0.4, // Membuat garis sedikit melengkung halus
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
