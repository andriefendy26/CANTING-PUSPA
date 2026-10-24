<?php

namespace App\Filament\Resources\Pasiens\Pages;

use App\Filament\Resources\Pasiens\PasienResource;
use App\Filament\Widgets\PertumbuhanPasienChart;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPasien extends ViewRecord
{
    protected static string $resource = PasienResource::class;

    protected function getHeaderActions(): array
    {
        return [EditAction::make()];
    }

    protected function getHeaderWidgets(): array
    {
        return [PertumbuhanPasienChart::class];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }
}