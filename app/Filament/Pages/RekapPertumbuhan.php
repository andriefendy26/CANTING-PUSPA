<?php

namespace App\Filament\Pages;

use App\Models\DataPasien;
use App\Models\Pasien;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class RekapPertumbuhan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTableCells;

    protected static ?string $navigationLabel = 'Rekap Tahunan';

    protected static ?string $title = 'Rekap BB & TB Tahunan';

    protected string $view = 'filament.pages.rekap-pertumbuhan';

    public int $tahun;

    public string $cari = '';

    public function mount(): void
    {
        $this->tahun = now()->year;
    }

    public function getBulan(): array
    {
        return DataPasien::BULAN;
    }

    public function getTahunOptions(): array
    {
        $years = DataPasien::query()->distinct()->orderByDesc('tahun')->pluck('tahun', 'tahun')->all();
        $years[now()->year] = now()->year;
        krsort($years);

        return $years;
    }

    public function getRows()
    {
        return Pasien::query()
            ->when($this->cari, fn ($q) => $q->where('nama_lengkap', 'like', "%{$this->cari}%"))
            ->with(['dataPasien' => fn ($q) => $q->where('tahun', $this->tahun)])
            ->orderBy('nama_lengkap')
            ->get()
            ->map(function (Pasien $p) {
                $p->setRelation('dataPasien', $p->dataPasien->keyBy('bulan'));

                return $p;
            });
    }
}