<?php

namespace App\Filament\Widgets;

use App\Models\DataPasien;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class PertumbuhanPasienChart extends ChartWidget
{
    // otomatis terisi di halaman View/Edit resource
    public ?Model $record = null;

    protected ?string $heading = 'Grafik Pertumbuhan (BB & TB)';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '350px';

    public ?string $filter = null;

    public function mount(): void
    {
        $this->filter = (string) now()->year;
    }

    protected function getFilters(): ?array
    {
        $years = $this->record?->dataPasien()
            ->distinct()->orderByDesc('tahun')->pluck('tahun', 'tahun')->all() ?? [];

        $years[now()->year] = now()->year;
        krsort($years);

        return $years;
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $data = $this->record
            ? $this->record->dataPasien()
                ->where('tahun', $this->filter ?? now()->year)
                ->get()->keyBy('bulan')
            : collect();

        $bb = $tb = [];
        foreach (range(1, 12) as $bulan) {
            // null = bulan tanpa data (garis tetap tersambung lewat spanGaps)
            $bb[] = $data->get($bulan)?->bb;
            $tb[] = $data->get($bulan)?->tb;
        }

        return [
            'datasets' => [
                [
                    'label' => 'BB (kg)',
                    'data' => $bb,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => '#f59e0b',
                    'yAxisID' => 'y',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'TB (cm)',
                    'data' => $tb,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => '#3b82f6',
                    'yAxisID' => 'y1',
                    'tension' => 0.3,
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'spanGaps' => true,
            'scales' => [
                'y' => [
                    'position' => 'left',
                    'title' => ['display' => true, 'text' => 'BB (kg)'],
                ],
                'y1' => [
                    'position' => 'right',
                    'grid' => ['drawOnChartArea' => false],
                    'title' => ['display' => true, 'text' => 'TB (cm)'],
                ],
            ],
        ];
    }
}