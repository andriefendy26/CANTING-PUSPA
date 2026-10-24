<x-filament-panels::page>
    <div class="flex flex-wrap items-end gap-4">
        <div class="w-40">
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="tahun">
                    @foreach ($this->getTahunOptions() as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
        <div class="w-64">
            <x-filament::input.wrapper>
                <x-filament::input type="text" placeholder="Cari nama..."
                                   wire:model.live.debounce.400ms="cari" />
            </x-filament::input.wrapper>
        </div>
    </div>

    <div class="overflow-x-auto rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <table class="w-full border-collapse text-sm">
            <thead class="bg-gray-50 text-xs uppercase dark:bg-white/5">
                <tr>
                    <th rowspan="2" class="sticky left-0 z-10 border bg-gray-50 px-3 py-2 text-left dark:border-white/10 dark:bg-gray-800">Nama</th>
                    <th rowspan="2" class="border px-3 py-2 dark:border-white/10">JK</th>
                    <th rowspan="2" class="border px-3 py-2 dark:border-white/10">Tgl Lahir</th>
                    @foreach ($this->getBulan() as $nama)
                        <th colspan="2" class="border px-3 py-2 text-center dark:border-white/10">{{ $nama }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($this->getBulan() as $nama)
                        <th class="border px-2 py-1 text-center dark:border-white/10">BB</th>
                        <th class="border px-2 py-1 text-center dark:border-white/10">TB</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($this->getRows() as $p)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="sticky left-0 whitespace-nowrap border bg-white px-3 py-2 font-medium dark:border-white/10 dark:bg-gray-900">
                            <a href="{{ \App\Filament\Resources\Pasiens\PasienResource::getUrl('view', ['record' => $p]) }}"
                               class="text-primary-600 hover:underline">
                                {{ $p->nama_lengkap }}
                            </a>
                        </td>
                        <td class="border px-3 py-2 text-center dark:border-white/10">{{ $p->jenis_kelamin }}</td>
                        <td class="whitespace-nowrap border px-3 py-2 dark:border-white/10">{{ $p->tanggal_lahir->format('d/m/y') }}</td>

                        @foreach ($this->getBulan() as $no => $nama)
                            @php $d = $p->dataPasien->get($no); @endphp
                            @if ($d && $d->keterangan && is_null($d->bb) && is_null($d->tb))
                                <td colspan="2" class="border bg-gray-100 px-2 py-2 text-center text-xs italic dark:border-white/10 dark:bg-white/5">
                                    {{ $d->keterangan }}
                                </td>
                            @else
                                <td class="border px-2 py-2 text-center dark:border-white/10">{{ $d?->bb !== null ? number_format($d->bb, 1) : '' }}</td>
                                <td class="border px-2 py-2 text-center dark:border-white/10">{{ $d?->tb !== null ? number_format($d->tb, 1) : '' }}</td>
                            @endif
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="27" class="px-3 py-6 text-center text-gray-500">Belum ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>