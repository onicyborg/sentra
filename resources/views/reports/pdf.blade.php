<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Surat</title>
    <style>
        @page { margin: 24mm 16mm; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        .header { text-align: center; margin-bottom: 16px; }
        .instansi { font-size: 16px; font-weight: bold; letter-spacing: 0.5px; }
        .title { font-size: 18px; font-weight: bold; margin-top: 4px; text-transform: uppercase; }
        .period { font-size: 12px; margin-top: 2px; color: #333; }
        .divider { border-top: 2px solid #000; margin: 10px 0 14px; }

        .summary { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .summary th, .summary td { border: 1px solid #333; padding: 6px 8px; }
        .summary th { background: #f0f0f0; text-align: left; }

        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #333; padding: 6px 8px; }
        .table th { background: #f0f0f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mt-12 { margin-top: 12px; }
        .mt-16 { margin-top: 16px; }
        .small { font-size: 11px; }
        .footer { position: fixed; bottom: -10mm; left: 0; right: 0; text-align: left; font-size: 11px; color: #444; }
    </style>
</head>
<body>
    <?php
        $from = $filters['from_date'] ?? '';
        $to = $filters['to_date'] ?? '';
        $jenis = $filters['jenis'] ?? 'semua';
        $title = $jenis === 'masuk' ? 'Laporan Surat Masuk' : ($jenis === 'keluar' ? 'Laporan Surat Keluar' : 'Laporan Rekap Surat');
        function fmtdate($d) { if (!$d) return '-'; $t = strtotime($d); return $t ? date('d/m/Y', $t) : $d; }
    ?>

    <div class="header">
        <div class="title">{{ $title }}</div>
        <div class="period">Periode: {{ fmtdate($from) }} s.d. {{ fmtdate($to) }}</div>
    </div>
    <div class="divider"></div>

    <table class="summary">
        <thead>
            <tr>
                <th>Total Surat Masuk</th>
                <th>Total Surat Keluar</th>
                <th>Total Arsip</th>
                <th>Total Surat Diproses</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-right">{{ number_format($summary['total_masuk'] ?? 0) }}</td>
                <td class="text-right">{{ number_format($summary['total_keluar'] ?? 0) }}</td>
                <td class="text-right">{{ number_format($summary['total_arsip'] ?? 0) }}</td>
                <td class="text-right">{{ number_format($summary['total_diproses'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="mt-12 small"><strong>Rekap Surat</strong></div>
    <table class="table mt-12">
        <thead>
            <tr>
                <th style="width: 28px">No</th>
                <th>Jenis Surat</th>
                <th>Nomor Surat</th>
                <th>Tanggal Surat / Terima</th>
                <th>Pengirim / Tujuan</th>
                <th>Perihal</th>
                <th>Status Akhir</th>
                <th>Tanggal Arsip</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            @foreach(($rekap_unarchived ?? []) as $r)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ strtoupper($r->jenis ?? '-') }}</td>
                    <td>{{ $r->nomor_surat ?? '-' }}</td>
                    <td class="text-center">{{ fmtdate($r->tanggal_surat ?? null) }}</td>
                    <td>{{ $r->pihak ?? '-' }}</td>
                    <td>{{ $r->perihal ?? '-' }}</td>
                    <td class="text-center">{{ $r->status ?? '-' }}</td>
                    <td class="text-center">-</td>
                </tr>
            @endforeach
            @foreach(($rekap_archived ?? []) as $r)
                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td class="text-center">{{ strtoupper($r->jenis ?? '-') }}</td>
                    <td>{{ $r->nomor_surat ?? '-' }}</td>
                    <td class="text-center">{{ fmtdate($r->tanggal_surat ?? null) }}</td>
                    <td>{{ $r->pihak ?? '-' }}</td>
                    <td>{{ $r->perihal ?? '-' }}</td>
                    <td class="text-center">{{ $r->status ?? '-' }}</td>
                    <td class="text-center">{{ fmtdate($r->archived_at ?? null) }}</td>
                </tr>
            @endforeach
            @if((($rekap_unarchived ?? collect())->count()) + (($rekap_archived ?? collect())->count()) === 0)
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <div>Tanggal cetak: {{ date('d/m/Y H:i') }} WIB</div>
        <div>Dokumen ini dihasilkan secara otomatis oleh sistem SENTRA.</div>
    </div>
</body>
</html>
