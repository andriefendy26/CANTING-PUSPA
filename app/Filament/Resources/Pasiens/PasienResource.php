<?php

namespace App\Filament\Resources\Pasiens;

use App\Filament\Resources\Pasiens\Pages\CreatePasien;
use App\Filament\Resources\Pasiens\Pages\EditPasien;
use App\Filament\Resources\Pasiens\Pages\ListPasiens;
use App\Filament\Resources\Pasiens\Pages\ViewPasien;
use App\Filament\Resources\Pasiens\RelationManagers\DataPasienRelationManager;
use App\Models\Pasien;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PasienResource extends Resource
{
    protected static ?string $model = Pasien::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $modelLabel = 'Pasien';

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Data Pasien')
                ->columns(2)
                ->schema([
                    TextInput::make('nama_lengkap')->required()->maxLength(255),
                    Select::make('jenis_kelamin')
                        ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                        ->required(),
                    TextInput::make('tempat_lahir'),
                    DatePicker::make('tanggal_lahir')->required()->maxDate(now()),
                    TextInput::make('no_telepon')->tel(),
                    Textarea::make('alamat')->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_lengkap')->label('Nama')->searchable()->sortable(),
                TextColumn::make('jenis_kelamin')->label('JK')
                    ->badge()
                    ->color(fn (string $state) => $state === 'L' ? 'info' : 'danger'),
                TextColumn::make('tanggal_lahir')->label('Tgl Lahir')->date('d/m/Y')->sortable(),
                TextColumn::make('usia'),
                TextColumn::make('no_telepon')->toggleable(),
            ])
            ->recordActions([
                ViewAction::make()->label('Grafik'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [DataPasienRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPasiens::route('/'),
            'create' => CreatePasien::route('/create'),
            'view'   => ViewPasien::route('/{record}'),
            'edit'   => EditPasien::route('/{record}/edit'),
        ];
    }
}