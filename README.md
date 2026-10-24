# Canting-Puspa

Sistem manajemen data pertumbuhan balita berbasis [Laravel](https://laravel.com) dengan panel admin [Filament](https://filamentphp.com).

## Fitur

- **Manajemen Pasien** — data nama lengkap, jenis kelamin, tempat/tanggal lahir, alamat, dan nomor telepon.
- **Data Pertumbuhan Bulanan** — pencatatan BB (kg) dan TB (cm) per bulan, atau keterangan (mis. lulus balita, PULKAM, pindah).
- **Grafik Pertumbuhan** — diagram garis BB dan TB tiap pasien (widget `PertumbuhanPasienChart`) pada halaman detail pasien.
- **Rekap Tahunan** — tabel rekapitulasi BB & TB seluruh pasien per bulan dalam suatu tahun, dengan filter tahun dan pencarian nama.
- **Panel Admin** — Filament dengan tema Aberdeen, autentikasi, dan navigasi Rekap Tahunan.

## Struktur Data

Tabel `pasien`:

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| nama_lengkap | string | |
| jenis_kelamin | enum L/P | Laki-laki / Perempuan |
| tempat_lahir | string nullable | |
| tanggal_lahir | date | Digunakan untuk hitung usia otomatis |
| alamat | text nullable | |
| no_telepon | string nullable | |
| timestamps | | |

Tabel `datapasien`:

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint PK | |
| id_pasien | bigint FK | ke `pasien`, cascade on delete |
| tahun | smallint unsigned | |
| bulan | tinyint unsigned | 1–12 |
| bb | decimal(5,2) nullable | kg |
| tb | decimal(5,1) nullable | cm |
| keterangan | string nullable | catatan non-numerik |
| timestamps | | |

Unique constraint: `id_pasien + tahun + bulan`.

Relasi: `Pasien` hasMany `DataPasien`; `DataPasien` belongsTo `Pasien`.

## Instalasi

Clone repository dan instal dependensi:

```bash
git clone <repository-url>
cd Canting-Puspa
composer install
```

Siapkan environment dan kunci aplikasi:

```bash
cp .env.example .env
php artisan key:generate
```

Konfigurasi database di `.env`, lalu jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

Jalankan server development:

```bash
php artisan serve
```

Akses panel admin di `/admin` dan login dengan kredensial yang telah dibuat.

## Teknologi

- Laravel
- Filament v3 (Panel, Resources, RelationManager, Widgets, Pages)
- Tema Aberdeen (Filament plugin)
- Chart.js (grafik pertumbuhan)
- Blade + Livewire (halaman Rekap Tahunan)

## Lisensi

MIT License.
