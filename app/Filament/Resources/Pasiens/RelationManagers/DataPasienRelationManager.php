<?php

namespace App\Filament\Resources\Pasiens\RelationManagers;

use App\Models\DataPasien;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;

class DataPasienRelationManager extends RelationManager
{
    protected static string $relationship = 'dataPasien';

    protected static ?string $title = 'Data BB & TB Bulanan';

    protected const KETERANGAN = [
        'normal'       => 'Normal',
        'lulus balita' => 'Lulus Balita',
        'pulkam'       => 'Pulkam',
        'pindah RT 7'  => 'Pindah RT 7',
    ];

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Periode')
                ->icon(Heroicon::OutlinedCalendarDays)
                // ->columns(2)
                ->schema([
                    TextInput::make('tahun')
                        ->numeric()
                        ->required()
                        ->default(now()->year)
                        ->minValue(2000)
                        ->maxValue(2100),
                    Select::make('bulan')
                        ->options(DataPasien::BULAN)
                        ->default(now()->month)
                        ->native(false)
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, Get $get) => $rule
                                ->where('id_pasien', $this->getOwnerRecord()->getKey())
                                ->where('tahun', $get('tahun')),
                        )
                        ->validationMessages(['unique' => 'Data bulan ini sudah ada.']),
                ])->columns(2),

            Section::make('Pengukuran')
                ->icon(Heroicon::OutlinedScale)
                ->description('Kosongkan jika bulan ini tidak ada pengukuran.')
                // ->columns(2)
                ->schema([
                    TextInput::make('bb')
                        ->label('Berat Badan (BB)')
                        ->numeric()
                        ->step(0.1)
                        ->minValue(0)
                        ->suffix('kg'),
                    TextInput::make('tb')
                        ->label('Tinggi Badan (TB)')
                        ->numeric()
                        ->step(0.1)
                        ->minValue(0)
                        ->suffix('cm'),
                ])->columns(2),

            Section::make('Keterangan')
                ->icon(Heroicon::OutlinedChatBubbleBottomCenterText)
                // ->columns(1)
                ->schema([
                    // Hanya pilihan UI, TIDAK disimpan ke database
                    Select::make('keterangan_opsi')
                        ->label('Status')
                        ->options(self::KETERANGAN + ['lainnya' => 'Lainnya (isi manual)'])
                        ->placeholder('Tanpa keterangan')
                        ->native(false)
                        ->live()
                        ->dehydrated(false)
                        ->afterStateHydrated(function (Select $component, ?Model $record) {
                            $nilai = $record?->keterangan;

                            $component->state(
                                blank($nilai) ? null
                                    : (array_key_exists($nilai, self::KETERANGAN) ? $nilai : 'lainnya')
                            );
                        })
                        ->afterStateUpdated(function (?string $state, Set $set) {
                            // isi kolom keterangan dari pilihan; 'lainnya' diisi manual
                            $set('keterangan', $state === 'lainnya' ? null : $state);
                        }),

                    // Kolom asli di database
                    TextInput::make('keterangan')
                        ->label('Keterangan Lainnya')
                        ->placeholder('Tulis keterangan secara manual')
                        ->maxLength(255)
                        ->visible(fn (Get $get) => $get('keterangan_opsi') === 'lainnya')
                        ->required(fn (Get $get) => $get('keterangan_opsi') === 'lainnya')
                        ->dehydratedWhenHidden(), // tetap tersimpan saat memakai pilihan di atas
                ])->columns(2),
        ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bulan')
            ->defaultSort(fn ($query) => $query->orderByDesc('tahun')->orderByDesc('bulan'))
            ->striped()
            ->columns([
                TextColumn::make('tahun')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('bulan')
                    ->formatStateUsing(fn ($state) => DataPasien::BULAN[$state] ?? $state)
                    ->weight('medium'),
                TextColumn::make('bb')
                    ->label('BB')
                    ->numeric(1)
                    ->suffix(' kg')
                    ->placeholder('-')
                    ->alignCenter(),
                TextColumn::make('tb')
                    ->label('TB')
                    ->numeric(1)
                    ->suffix(' cm')
                    ->placeholder('-')
                    ->alignCenter(),
                TextColumn::make('keterangan')
                    ->badge()
                    ->placeholder('-')
                    ->formatStateUsing(fn (?string $state) => self::KETERANGAN[$state] ?? $state)
                    ->color(fn (?string $state) => match ($state) {
                        'normal'       => 'success',
                        'lulus balita' => 'info',
                        'pulkam'       => 'warning',
                        'pindah RT 7'  => 'gray',
                        default        => 'primary',
                    }),
            ])
            ->filters([
                SelectFilter::make('tahun')
                    ->options(fn () => $this->getOwnerRecord()->dataPasien()
                        ->distinct()->orderByDesc('tahun')->pluck('tahun', 'tahun')->all()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Data')
                    ->icon(Heroicon::OutlinedPlus)
                    ->modalWidth(Width::Large),
            ])
            ->recordActions([
                EditAction::make()->modalWidth(Width::Large),
                DeleteAction::make(),
            ]);
    }
}