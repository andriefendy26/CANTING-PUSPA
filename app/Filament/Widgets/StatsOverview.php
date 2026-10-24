<?php

namespace App\Filament\Widgets;

use App\Models\DataPasien;
use App\Models\Pasien;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pasien', Pasien::count())
                ->description('Seluruh data pasien tercatat')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Pasien Laki-Laki', Pasien::where('jenis_kelamin', 'L')->count())
                ->description('Jumlah pasien jenis kelamin laki-laki')
                ->descriptionIcon('heroicon-m-user')
                ->color('info'),

            Stat::make('Pasien Perempuan', Pasien::where('jenis_kelamin', 'P')->count())
                ->description('Jumlah pasien jenis kelamin perempuan')
                ->descriptionIcon('heroicon-m-user')
                ->color('danger'),

            Stat::make('Total Kasus Stunting', Pasien::whereHas('dataPasien', fn ($q) => $q->where('keterangan', 'like', '%stunting%'))->count())
                ->description('Pasien dengan setidaknya satu catatan stunting')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Total Stunting', DataPasien::where('keterangan', 'like', '%stunting%')->count())
                ->description('Jumlah total catatan stunting')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
