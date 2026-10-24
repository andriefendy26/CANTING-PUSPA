<?php

namespace App\Filament\Resources\Pasiens\RelationManagers;

use App\Models\DataPasien;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class DataPasienRelationManager extends RelationManager
{
    protected static string $relationship = 'dataPasien';

    protected static ?string $title = 'Data BB & TB Bulanan';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('tahun')
                ->numeric()->required()
                ->default(now()->year)
                ->minValue(2000)->maxValue(2100),
            Select::make('bulan')
                ->options(DataPasien::BULAN)
                ->default(now()->month)
                ->required()
                ->unique(
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule, callable $get) => $rule
                        ->where('id_pasien', $this->getOwnerRecord()->getKey())
                        ->where('tahun', $get('tahun')),
                )
                ->validationMessages(['unique' => 'Data bulan ini sudah ada.']),
            TextInput::make('bb')->label('BB (kg)')->numeric()->step(0.1)->suffix('kg'),
            TextInput::make('tb')->label('TB (cm)')->numeric()->step(0.1)->suffix('cm'),
            Select::make('keterangan')
                ->options([
                    'lulus balita' => 'Lulus Balita',
                    'pulkam' => 'Pulkam',
                    'pindah RT 7' => 'Pindah RT 7',
                    'normal' => 'Normal',
                    'lainnya' => 'Lainnya',
                ])
                ->placeholder('Keterangan (opsional)')
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state !== 'lainnya') {
                        $set('keterangan_lainnya', null);
                    }
                }),

            TextInput::make('keterangan_lainnya')
                ->label('Keterangan Lainnya')
                ->placeholder('Masukkan keterangan secara manual')
                ->visible(fn (callable $get) => $get('keterangan') === 'lainnya')
                ->required(fn (callable $get) => $get('keterangan') === 'lainnya'),
            // TextInput::make('keterangan')
            //     ->placeholder('lulus balita / pulkam / pindah RT 7 / normal')
            //     ->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bulan')
            ->defaultSort('tahun', 'desc')
            ->defaultSort(fn ($query) => $query->orderByDesc('tahun')->orderByDesc('bulan'))
            ->columns([
                TextColumn::make('tahun')->sortable(),
                TextColumn::make('bulan')
                    ->formatStateUsing(fn ($state) => DataPasien::BULAN[$state] ?? $state),
                TextColumn::make('bb')->label('BB (kg)')->numeric(1),
                TextColumn::make('tb')->label('TB (cm)')->numeric(1),
                TextColumn::make('keterangan')->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('tahun')
                    ->options(fn () => $this->getOwnerRecord()->dataPasien()
                        ->distinct()->orderByDesc('tahun')->pluck('tahun', 'tahun')->all()),
            ])
            ->headerActions([CreateAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}