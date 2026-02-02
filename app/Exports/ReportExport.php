<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /** @var \Illuminate\Support\Collection */
    protected $rows;
    /** @var array<string,array<string>> keyed by "jenis:surat_id" */
    protected $lampiranMap = [];

    public function __construct(Collection $rows, array $lampiranMap = [])
    {
        $this->rows = $rows->values();
        $this->lampiranMap = $lampiranMap;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Jenis Surat',
            'Nomor Surat',
            'Tanggal Surat / Terima',
            'Pengirim / Tujuan',
            'Perihal',
            'Status Akhir',
            'Tanggal Arsip',
            'Lampiran',
        ];
    }

    public function map($row): array
    {
        static $no = 0; $no++;
        $fmt = function ($d) { if (!$d) return null; try { return \Carbon\Carbon::parse($d)->format('Y-m-d'); } catch (\Throwable $e) { return $d; } };
        $key = ($row->jenis ?? '').':'.($row->surat_id ?? '');
        $links = $this->lampiranMap[$key] ?? [];
        $lampiranCell = implode("\n", $links); // multiple URLs separated by newline

        return [
            $no,
            strtoupper($row->jenis ?? ''),
            $row->nomor_surat ?? '',
            $fmt($row->tanggal_surat ?? null),
            $row->pihak ?? '',
            $row->perihal ?? '',
            $row->status ?? '',
            $fmt($row->archived_at ?? null),
            $lampiranCell,
        ];
    }
}
