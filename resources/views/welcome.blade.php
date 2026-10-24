<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Canting-Puspa — Catat dan pantau pertumbuhan balita</title>
    <meta name="description" content="Canting-Puspa membantu mencatat berat dan tinggi badan balita tiap bulan, menampilkan grafik pertumbuhan, dan merekap data seluruh pasien per tahun.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --paper: #f2f7f4;
            --surface: #ffffff;
            --ink: #14302b;
            --muted: #4f6a64;
            --teal: #1d5d54;
            --teal-dark: #134640;
            --gold: #f0b13a;
            --line: #cfdfd8;
            --display: "Bricolage Grotesque", "Segoe UI", system-ui, sans-serif;
            --body: "Figtree", "Segoe UI", system-ui, sans-serif;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --paper: #0f211e;
                --surface: #162d29;
                --ink: #e8f2ee;
                --muted: #9db8b0;
                --teal: #6fc7b8;
                --teal-dark: #8fdacd;
                --line: #2b4741;
            }
        }
        *, *::before, *::after { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            background: var(--paper);
            color: var(--ink);
            font-family: var(--body);
            font-size: 1.0625rem;
            line-height: 1.65;
        }
        a { color: inherit; }
        :focus-visible { outline: 3px solid var(--gold); outline-offset: 3px; }
        .wrap { width: min(1120px, 100% - 2.5rem); margin-inline: auto; }

        h1, h2, h3 { font-family: var(--display); line-height: 1.1; margin: 0; letter-spacing: -0.02em; }
        h1 { font-size: clamp(2.5rem, 6vw, 4.5rem); font-weight: 800; }
        h2 { font-size: clamp(1.8rem, 3.6vw, 2.6rem); font-weight: 700; }
        h3 { font-size: 1.25rem; font-weight: 700; letter-spacing: -0.01em; }
        p { margin: 0; }

        /* Tombol */
        .btn {
            display: inline-block;
            padding: 0.85rem 1.6rem;
            border-radius: 999px;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid var(--teal);
            transition: background .2s, color .2s;
        }
        .btn-primary { background: var(--teal); color: #fff; }
        .btn-primary:hover { background: var(--teal-dark); border-color: var(--teal-dark); }
        @media (prefers-color-scheme: dark) { .btn-primary { color: #0f211e; } }
        .btn-ghost { color: var(--teal); }
        .btn-ghost:hover { background: var(--teal); color: #fff; }

        /* Header */
        header { padding-block: 1.25rem; }
        header .wrap { display: flex; align-items: center; justify-content: space-between; gap: 1rem; }
        .brand { font-family: var(--display); font-weight: 800; font-size: 1.35rem; text-decoration: none; display: flex; align-items: center; gap: .6rem; }
        .brand-dot { width: 14px; height: 14px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 0 5px color-mix(in srgb, var(--gold) 30%, transparent); }
        nav { display: flex; align-items: center; gap: 1.75rem; }
        nav a:not(.btn) { text-decoration: none; color: var(--muted); font-weight: 500; }
        nav a:not(.btn):hover { color: var(--ink); }
        nav .btn { padding: .55rem 1.2rem; }
        @media (max-width: 760px) { nav a:not(.btn) { display: none; } }

        /* Hero */
        .hero { padding-block: clamp(2.5rem, 7vw, 5.5rem); }
        .hero .wrap { display: grid; grid-template-columns: 1.05fr 1fr; gap: clamp(2rem, 5vw, 4rem); align-items: center; }
        .hero p.lead { margin-top: 1.5rem; color: var(--muted); max-width: 34rem; font-size: 1.15rem; }
        .hero .actions { margin-top: 2rem; display: flex; flex-wrap: wrap; gap: .85rem; }
        @media (max-width: 900px) { .hero .wrap { grid-template-columns: 1fr; } }

        /* Kartu grafik: titik-titik malam ala canting */
        .chart-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 1.5rem;
            background-image: radial-gradient(color-mix(in srgb, var(--teal) 14%, transparent) 1.5px, transparent 1.5px);
            background-size: 18px 18px;
        }
        .chart-head { display: flex; justify-content: space-between; align-items: baseline; gap: 1rem; flex-wrap: wrap; }
        .chart-head strong { font-family: var(--display); font-size: 1.1rem; }
        .chart-head span { font-size: .875rem; color: var(--muted); }
        .chart-card svg { width: 100%; height: auto; display: block; margin-top: .75rem; }
        .legend { display: flex; gap: 1.25rem; font-size: .875rem; color: var(--muted); margin-top: .5rem; }
        .legend i { display: inline-block; width: 18px; height: 4px; border-radius: 2px; margin-right: .4rem; vertical-align: middle; }
        .axis text { fill: var(--muted); font-size: 11px; font-family: var(--body); }
        .grid-line { stroke: var(--line); stroke-width: 1; }
        .line-bb { stroke: var(--teal); }
        .line-tb { stroke: var(--gold); }
        .dot-bb { fill: var(--teal); }
        .dot-tb { fill: var(--gold); }
        .draw { stroke-dasharray: 700; stroke-dashoffset: 700; animation: draw 1.6s .3s ease-out forwards; }
        @keyframes draw { to { stroke-dashoffset: 0; } }
        @media (prefers-reduced-motion: reduce) { .draw { animation: none; stroke-dashoffset: 0; } html { scroll-behavior: auto; } }

        /* Seksi umum */
        section { padding-block: clamp(3rem, 7vw, 5.5rem); }
        .section-head { max-width: 40rem; margin-bottom: 2.5rem; }
        .section-head p { margin-top: 1rem; color: var(--muted); }
        .band { background: var(--surface); border-block: 1px solid var(--line); }

        /* Fitur */
        .features { display: grid; grid-template-columns: repeat(6, 1fr); gap: 1.25rem; }
        .feature { grid-column: span 2; padding-top: 1.1rem; border-top: 3px solid var(--teal); }
        .feature:nth-child(1), .feature:nth-child(2) { grid-column: span 3; }
        .feature:nth-child(1) { border-top-color: var(--gold); }
        .feature p { margin-top: .6rem; color: var(--muted); }
        @media (max-width: 800px) { .features { grid-template-columns: 1fr; } .feature, .feature:nth-child(1), .feature:nth-child(2) { grid-column: auto; } }

        /* Alur kerja (urutan nyata) */
        .steps { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(3, 1fr); gap: 2rem; counter-reset: step; }
        .steps li { counter-increment: step; position: relative; padding-left: 3.5rem; }
        .steps li::before {
            content: counter(step);
            position: absolute; left: 0; top: 0;
            width: 2.4rem; height: 2.4rem; border-radius: 50%;
            display: grid; place-items: center;
            background: var(--gold); color: #14302b;
            font-family: var(--display); font-weight: 800;
        }
        .steps p { margin-top: .4rem; color: var(--muted); }
        @media (max-width: 800px) { .steps { grid-template-columns: 1fr; } }

        /* Pratinjau rekap */
        .recap { display: grid; grid-template-columns: 1fr 1.4fr; gap: clamp(2rem, 5vw, 4rem); align-items: center; }
        .recap ul { margin: 1.25rem 0 0; padding-left: 1.2rem; color: var(--muted); }
        .recap li + li { margin-top: .4rem; }
        .table-scroll { overflow-x: auto; border: 1px solid var(--line); border-radius: 16px; background: var(--paper); }
        table { width: 100%; border-collapse: collapse; font-size: .95rem; min-width: 460px; }
        th, td { padding: .8rem 1rem; text-align: left; border-bottom: 1px solid var(--line); }
        th { font-family: var(--display); font-weight: 700; background: color-mix(in srgb, var(--teal) 10%, transparent); }
        tr:last-child td { border-bottom: 0; }
        td.num { font-variant-numeric: tabular-nums; }
        td.note { color: var(--muted); font-style: italic; }
        .caption { margin-top: .75rem; font-size: .875rem; color: var(--muted); }
        @media (max-width: 900px) { .recap { grid-template-columns: 1fr; } }

        /* Data */
        .data { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        .data article { border: 1px solid var(--line); border-radius: 16px; padding: 1.5rem; background: var(--paper); }
        .data ul { list-style: none; margin: 1rem 0 0; padding: 0; }
        .data li { display: flex; justify-content: space-between; gap: 1rem; padding: .45rem 0; border-bottom: 1px dashed var(--line); font-size: .975rem; }
        .data li:last-child { border-bottom: 0; }
        .data li span:last-child { color: var(--muted); text-align: right; }
        @media (max-width: 800px) { .data { grid-template-columns: 1fr; } }

        /* Teknologi */
        .stack { display: flex; flex-wrap: wrap; gap: .6rem; margin: 0; padding: 0; list-style: none; }
        .stack li { padding: .45rem 1rem; border: 1px solid var(--line); border-radius: 999px; background: var(--surface); font-weight: 500; }

        /* CTA */
        .cta { background: var(--teal); color: #fff; text-align: center; border-radius: 28px; padding: clamp(2.5rem, 6vw, 4rem) 1.5rem; }
        .cta p { margin: 1rem auto 1.75rem; max-width: 30rem; opacity: .9; }
        .cta .btn { background: var(--gold); border-color: var(--gold); color: #14302b; }
        .cta .btn:hover { background: #fff; border-color: #fff; }
        @media (prefers-color-scheme: dark) { .cta { background: #1d5d54; color: #fff; } }

        footer { padding-block: 2rem 2.5rem; color: var(--muted); font-size: .925rem; }
        footer .wrap { display: flex; justify-content: space-between; flex-wrap: wrap; gap: .5rem; }
    </style>
</head>
<body>

    <header>
        <div class="wrap">
            <a href="{{ url('/') }}" class="brand"><span class="brand-dot" aria-hidden="true"></span>Canting-Puspa</a>
            <nav aria-label="Navigasi utama">
                <a href="#fitur">Fitur</a>
                <a href="#alur">Cara kerja</a>
                <a href="#rekap">Rekap tahunan</a>
                <a href="#data">Struktur data</a>
                <a href="{{ url('/admin') }}" class="btn btn-primary">Masuk</a>
            </nav>
        </div>
    </header>

    <main>
        {{-- HERO --}}
        <div class="hero">
            <div class="wrap">
                <div>
                    <h1>Pertumbuhan balita, tercatat rapi tiap bulan.</h1>
                    <p class="lead">
                        Canting-Puspa menyimpan data pasien, berat dan tinggi badan bulanan, lalu mengubahnya
                        menjadi grafik dan rekap tahunan yang mudah dibaca.
                    </p>
                    <div class="actions">
                        <a href="{{ url('/admin') }}" class="btn btn-primary">Masuk ke panel admin</a>
                        <a href="#fitur" class="btn btn-ghost">Lihat fitur</a>
                    </div>
                </div>

                <figure class="chart-card" style="margin:0" aria-label="Contoh grafik pertumbuhan berat dan tinggi badan">
                    <div class="chart-head">
                        <strong>Grafik pertumbuhan pasien</strong>
                        <span>Contoh data, 12 bulan</span>
                    </div>
                    <svg viewBox="0 0 460 260" role="img" aria-label="Garis berat badan dan tinggi badan naik bertahap dari Januari sampai Desember">
                        <g class="axis">
                            <line class="grid-line" x1="40" y1="30" x2="450" y2="30"/>
                            <line class="grid-line" x1="40" y1="90" x2="450" y2="90"/>
                            <line class="grid-line" x1="40" y1="150" x2="450" y2="150"/>
                            <line class="grid-line" x1="40" y1="210" x2="450" y2="210"/>
                            <text x="32" y="34" text-anchor="end">90</text>
                            <text x="32" y="94" text-anchor="end">65</text>
                            <text x="32" y="154" text-anchor="end">40</text>
                            <text x="32" y="214" text-anchor="end">15</text>
                            <text x="40" y="236">Jan</text>
                            <text x="168" y="236">Apr</text>
                            <text x="296" y="236">Jul</text>
                            <text x="410" y="236">Des</text>
                        </g>
                        {{-- TB (cm) --}}
                        <polyline class="line-tb draw" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                            points="40,118 77,112 114,106 151,100 188,95 225,89 262,84 299,79 336,74 373,69 410,64 447,60"/>
                        <circle class="dot-tb" cx="447" cy="60" r="5"/>
                        {{-- BB (kg) --}}
                        <polyline class="line-bb draw" fill="none" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                            points="40,196 77,192 114,187 151,184 188,178 225,176 262,170 299,167 336,162 373,160 410,155 447,152"/>
                        <circle class="dot-bb" cx="447" cy="152" r="5"/>
                    </svg>
                    <div class="legend">
                        <span><i style="background:var(--teal)"></i>BB (kg)</span>
                        <span><i style="background:var(--gold)"></i>TB (cm)</span>
                    </div>
                </figure>
            </div>
        </div>

        {{-- FITUR --}}
        <section id="fitur" class="band">
            <div class="wrap">
                <div class="section-head">
                    <h2>Semua yang dibutuhkan untuk memantau balita</h2>
                    <p>Dari data diri pasien sampai ringkasan satu tahun penuh, semuanya ada dalam satu panel.</p>
                </div>
                <div class="features">
                    <article class="feature">
                        <h3>Manajemen pasien</h3>
                        <p>Simpan nama lengkap, jenis kelamin, tempat dan tanggal lahir, alamat, serta nomor telepon. Usia dihitung otomatis dari tanggal lahir.</p>
                    </article>
                    <article class="feature">
                        <h3>Data pertumbuhan bulanan</h3>
                        <p>Catat berat badan (kg) dan tinggi badan (cm) tiap bulan. Bila tidak ada pengukuran, isi keterangan seperti lulus balita, PULKAM, atau pindah.</p>
                    </article>
                    <article class="feature">
                        <h3>Grafik pertumbuhan</h3>
                        <p>Diagram garis BB dan TB tampil di halaman detail setiap pasien, sehingga tren terlihat sekilas.</p>
                    </article>
                    <article class="feature">
                        <h3>Rekap tahunan</h3>
                        <p>Tabel BB dan TB seluruh pasien per bulan dalam satu tahun, lengkap dengan filter tahun dan pencarian nama.</p>
                    </article>
                    <article class="feature">
                        <h3>Panel admin yang aman</h3>
                        <p>Dibangun dengan Filament dan tema Aberdeen. Hanya pengguna yang sudah masuk yang dapat mengakses data.</p>
                    </article>
                </div>
            </div>
        </section>

        {{-- ALUR --}}
        <section id="alur">
            <div class="wrap">
                <div class="section-head">
                    <h2>Tiga langkah dari catatan ke gambaran utuh</h2>
                </div>
                <ol class="steps">
                    <li>
                        <h3>Daftarkan pasien</h3>
                        <p>Isi data diri balita satu kali. Tanggal lahir menjadi dasar perhitungan usia.</p>
                    </li>
                    <li>
                        <h3>Catat tiap bulan</h3>
                        <p>Masukkan BB dan TB pada bulan berjalan. Satu pasien hanya memiliki satu catatan per bulan.</p>
                    </li>
                    <li>
                        <h3>Baca grafik dan rekap</h3>
                        <p>Lihat tren pasien di halaman detail, atau bandingkan semua pasien lewat rekap tahunan.</p>
                    </li>
                </ol>
            </div>
        </section>

        {{-- REKAP --}}
        <section id="rekap" class="band">
            <div class="wrap recap">
                <div>
                    <h2>Rekap tahunan dalam satu tabel</h2>
                    <ul>
                        <li>Seluruh pasien, Januari sampai Desember</li>
                        <li>Filter berdasarkan tahun</li>
                        <li>Cari pasien dengan nama</li>
                        <li>Keterangan seperti PULKAM tampil di bulan yang sesuai</li>
                    </ul>
                </div>
                <div>
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr><th>Nama</th><th>Jan</th><th>Feb</th><th>Mar</th></tr>
                            </thead>
                            <tbody>
                                <tr><td>Balita A</td><td class="num">9,2 kg / 72,0 cm</td><td class="num">9,5 kg / 72,8 cm</td><td class="num">9,7 kg / 73,5 cm</td></tr>
                                <tr><td>Balita B</td><td class="num">11,0 kg / 80,5 cm</td><td class="num">11,3 kg / 81,0 cm</td><td class="note">Pindah</td></tr>
                                <tr><td>Balita C</td><td class="num">13,4 kg / 91,0 cm</td><td class="note">Lulus balita</td><td class="note">Lulus balita</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="caption">Tampilan contoh dengan data fiktif.</p>
                </div>
            </div>
        </section>

        {{-- DATA --}}
        <section id="data">
            <div class="wrap">
                <div class="section-head">
                    <h2>Struktur data yang sederhana</h2>
                    <p>Dua tabel yang saling terhubung. Menghapus pasien ikut menghapus seluruh catatan pertumbuhannya.</p>
                </div>
                <div class="data">
                    <article>
                        <h3>Pasien</h3>
                        <ul>
                            <li><span>nama_lengkap</span><span>teks</span></li>
                            <li><span>jenis_kelamin</span><span>L / P</span></li>
                            <li><span>tempat_lahir</span><span>teks, opsional</span></li>
                            <li><span>tanggal_lahir</span><span>tanggal</span></li>
                            <li><span>alamat</span><span>teks panjang, opsional</span></li>
                            <li><span>no_telepon</span><span>teks, opsional</span></li>
                        </ul>
                    </article>
                    <article>
                        <h3>Data pasien</h3>
                        <ul>
                            <li><span>id_pasien</span><span>relasi ke pasien</span></li>
                            <li><span>tahun</span><span>angka</span></li>
                            <li><span>bulan</span><span>1 sampai 12</span></li>
                            <li><span>bb</span><span>kg, opsional</span></li>
                            <li><span>tb</span><span>cm, opsional</span></li>
                            <li><span>keterangan</span><span>teks, opsional</span></li>
                        </ul>
                    </article>
                </div>
            </div>
        </section>

        {{-- TEKNOLOGI --}}
        <section class="band" style="padding-block: 3rem;">
            <div class="wrap">
                <h2 style="font-size:1.5rem; margin-bottom:1.25rem;">Dibangun dengan</h2>
                <ul class="stack">
                    <li>Laravel</li>
                    <li>Filament v3</li>
                    <li>Tema Aberdeen</li>
                    <li>Chart.js</li>
                    <li>Blade</li>
                    <li>Livewire</li>
                </ul>
            </div>
        </section>

        {{-- CTA --}}
        <section>
            <div class="wrap">
                <div class="cta">
                    <h2>Siap mencatat pertumbuhan balita?</h2>
                    <p>Masuk dengan akun admin untuk mulai menambah pasien dan mengisi data bulan ini.</p>
                    <a href="{{ url('/admin') }}" class="btn">Masuk ke panel admin</a>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="wrap">
            <span>&copy; {{ date('Y') }} Canting-Puspa</span>
            <span>Dirilis dengan lisensi MIT</span>
        </div>
    </footer>

</body>
</html>