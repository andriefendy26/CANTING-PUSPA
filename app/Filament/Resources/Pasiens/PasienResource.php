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
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PasienResource extends Resource
{
    protected static ?string $model = Pasien::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $modelLabel = 'Pasien';

    protected static ?string $pluralModelLabel = 'Data Pasien';

    protected static ?string $recordTitleAttribute = 'nama_lengkap';

    protected static ?int $navigationSort = 1;

    /* ---------------------------------------------------------------
     |  FORM (create / edit)
     * ------------------------------------------------------------- */
    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas Pasien')
                ->description('Data diri pasien balita.')
                ->icon(Heroicon::OutlinedIdentification)
                ->columns(2)
                ->schema([
                    TextInput::make('nama_lengkap')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Select::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                        ->native(false)
                        ->required(),
                    DatePicker::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->maxDate(now())
                        ->required(),
                    TextInput::make('tempat_lahir')
                        ->label('Tempat Lahir')
                        ->columnSpanFull(),
                ]),

            Section::make('Kontak & Alamat')
                ->icon(Heroicon::OutlinedMapPin)
                ->columns(2)
                ->schema([
                    TextInput::make('no_telepon')
                        ->label('No. Telepon')
                        ->tel()
                        ->prefixIcon(Heroicon::OutlinedPhone)
                        ->columnSpanFull(),
                    Textarea::make('alamat')
                        ->label('Alamat')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    /* ---------------------------------------------------------------
     |  INFOLIST (halaman detail / View)
     * ------------------------------------------------------------- */
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas Pasien')
                ->icon(Heroicon::OutlinedIdentification)
                ->columns(['default' => 1, 'md' => 3])
                ->schema([
                    TextEntry::make('nama_lengkap')
                        ->label('Nama Lengkap')
                        ->weight('bold')
                        ->columnSpan(['md' => 2]),
                    TextEntry::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->badge()
                        ->formatStateUsing(fn (string $state) => $state === 'L' ? 'Laki-laki' : 'Perempuan')
                        ->color(fn (string $state) => $state === 'L' ? 'info' : 'danger'),
                    TextEntry::make('tempat_lahir')
                        ->label('Tempat Lahir')
                        ->placeholder('-'),
                    TextEntry::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->date('d F Y'),
                    TextEntry::make('usia')
                        ->label('Usia')
                        ->badge()
                        ->color('success'),
                    TextEntry::make('no_telepon')
                        ->label('No. Telepon')
                        ->placeholder('-'),
                    TextEntry::make('alamat')
                        ->label('Alamat')
                        ->placeholder('-')
                        ->columnSpan(['md' => 2]),
                ]),
        ]);
    }

    /* ---------------------------------------------------------------
     |  TABLE
     * ------------------------------------------------------------- */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('nama_lengkap')
            ->striped()
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (Pasien $record) => $record->no_telepon),
                TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (string $state) => $state === 'L' ? 'info' : 'danger'),
                TextColumn::make('tanggal_lahir')
                    ->label('Tgl Lahir')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('usia')
                    ->badge()
                    ->color('success'),
                TextColumn::make('alamat')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Grafik')
                    ->icon(Heroicon::OutlinedChartBar),
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