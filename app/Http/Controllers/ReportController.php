<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('can:report.read');
    }

    public function index(Request $request)
    {
        // Filters with defaults: current month
        $from = $request->get('from_date');
        $to = $request->get('to_date');
        $jenis = strtolower((string) $request->get('jenis')) ?: 'semua';

        if (!$from || !$to) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
            $from = $from ?: $start->toDateString();
            $to = $to ?: $end->toDateString();
        }

        // Build datasets separately: archived vs unarchived
        // Surat Masuk - Unarchived
        $smUnarchived = DB::table('surat_masuk as sm')
            ->leftJoin('arsip as a', function ($j) {
                $j->on('a.surat_id', '=', 'sm.id')->where('a.jenis_surat', 'masuk');
            })
            ->whereBetween('sm.tanggal_terima', [$from, $to])
            ->whereNull('a.id')
            ->select([
                'sm.id as surat_id',
                'sm.nomor_surat',
                'sm.tanggal_terima as tanggal_surat',
                'sm.pengirim as pihak',
                'sm.perihal',
                'sm.status',
                DB::raw('NULL as archived_at'),
            ])->get()->map(function ($r) {
                $r->jenis = 'masuk';
                return $r;
            });

        // Surat Masuk - Archived
        $smArchived = DB::table('surat_masuk as sm')
            ->join('arsip as a', function ($j) {
                $j->on('a.surat_id', '=', 'sm.id')->where('a.jenis_surat', 'masuk');
            })
            ->whereBetween('a.archived_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->select([
                'sm.id as surat_id',
                'sm.nomor_surat',
                'sm.tanggal_terima as tanggal_surat',
                'sm.pengirim as pihak',
                'sm.perihal',
                'sm.status',
                'a.archived_at',
            ])->get()->map(function ($r) {
                $r->jenis = 'masuk';
                return $r;
            });

        // Surat Keluar - Unarchived
        $skUnarchived = DB::table('surat_keluar as sk')
            ->leftJoin('arsip as a', function ($j) {
                $j->on('a.surat_id', '=', 'sk.id')->where('a.jenis_surat', 'keluar');
            })
            ->whereBetween('sk.tanggal_surat', [$from, $to])
            ->whereNull('a.id')
            ->select([
                'sk.id as surat_id',
                'sk.nomor_surat',
                'sk.tanggal_surat',
                'sk.tujuan as pihak',
                'sk.perihal',
                'sk.status',
                DB::raw('NULL as archived_at'),
            ])->get()->map(function ($r) {
                $r->jenis = 'keluar';
                return $r;
            });

        // Surat Keluar - Archived
        $skArchived = DB::table('surat_keluar as sk')
            ->join('arsip as a', function ($j) {
                $j->on('a.surat_id', '=', 'sk.id')->where('a.jenis_surat', 'keluar');
            })
            ->whereBetween('a.archived_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->select([
                'sk.id as surat_id',
                'sk.nomor_surat',
                'sk.tanggal_surat',
                'sk.tujuan as pihak',
                'sk.perihal',
                'sk.status',
                'a.archived_at',
            ])->get()->map(function ($r) {
                $r->jenis = 'keluar';
                return $r;
            });

        // Merge datasets according to jenis filter
        if ($jenis === 'masuk') {
            $rekapUnarchived = $smUnarchived;
            $rekapArchived = $smArchived;
        } elseif ($jenis === 'keluar') {
            $rekapUnarchived = $skUnarchived;
            $rekapArchived = $skArchived;
        } else {
            $rekapUnarchived = $smUnarchived->concat($skUnarchived);
            $rekapArchived = $smArchived->concat($skArchived);
        }

        // Summary cards
        $totalMasuk = DB::table('surat_masuk')->whereBetween('tanggal_terima', [$from, $to])->count();
        $totalKeluar = DB::table('surat_keluar')->whereBetween('tanggal_surat', [$from, $to])->count();
        $totalArsip = DB::table('arsip')->whereBetween('archived_at', [$from.' 00:00:00', $to.' 23:59:59'])->count();
        $totalDiproses = $totalMasuk + $totalKeluar;

        // Normalize keys
        $rekapArchived = $rekapArchived->values();
        $rekapUnarchived = $rekapUnarchived->values();

        return view('reports.index', [
            'filters' => [
                'from_date' => $from,
                'to_date' => $to,
                'jenis' => $jenis,
            ],
            'summary' => [
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'total_arsip' => $totalArsip,
                'total_diproses' => $totalDiproses,
            ],
            'rekap_archived' => $rekapArchived,
            'rekap_unarchived' => $rekapUnarchived,
        ]);
    }

    public function exportPdf(Request $request)
    {
        // Reuse the same filter and data-building logic as index()
        $subRequest = Request::create('/laporan', 'GET', $request->all());
        // Manually call index logic but capture its computed variables instead of returning view directly

        $from = $request->get('from_date');
        $to = $request->get('to_date');
        $jenis = strtolower((string) $request->get('jenis')) ?: 'semua';
        if (!$from || !$to) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
            $from = $from ?: $start->toDateString();
            $to = $to ?: $end->toDateString();
        }

        // Build datasets (copy from index)
        $smUnarchived = DB::table('surat_masuk as sm')
            ->leftJoin('arsip as a', function ($j) { $j->on('a.surat_id', '=', 'sm.id')->where('a.jenis_surat', 'masuk'); })
            ->whereBetween('sm.tanggal_terima', [$from, $to])
            ->whereNull('a.id')
            ->select(['sm.id as surat_id','sm.nomor_surat','sm.tanggal_terima as tanggal_surat','sm.pengirim as pihak','sm.perihal','sm.status', DB::raw('NULL as archived_at')])
            ->get()->map(function($r){ $r->jenis='masuk'; return $r; });
        $smArchived = DB::table('surat_masuk as sm')
            ->join('arsip as a', function ($j) { $j->on('a.surat_id', '=', 'sm.id')->where('a.jenis_surat', 'masuk'); })
            ->whereBetween('a.archived_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->select(['sm.id as surat_id','sm.nomor_surat','sm.tanggal_terima as tanggal_surat','sm.pengirim as pihak','sm.perihal','sm.status','a.archived_at'])
            ->get()->map(function($r){ $r->jenis='masuk'; return $r; });
        $skUnarchived = DB::table('surat_keluar as sk')
            ->leftJoin('arsip as a', function ($j) { $j->on('a.surat_id', '=', 'sk.id')->where('a.jenis_surat', 'keluar'); })
            ->whereBetween('sk.tanggal_surat', [$from, $to])
            ->whereNull('a.id')
            ->select(['sk.id as surat_id','sk.nomor_surat','sk.tanggal_surat','sk.tujuan as pihak','sk.perihal','sk.status', DB::raw('NULL as archived_at')])
            ->get()->map(function($r){ $r->jenis='keluar'; return $r; });
        $skArchived = DB::table('surat_keluar as sk')
            ->join('arsip as a', function ($j) { $j->on('a.surat_id', '=', 'sk.id')->where('a.jenis_surat', 'keluar'); })
            ->whereBetween('a.archived_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->select(['sk.id as surat_id','sk.nomor_surat','sk.tanggal_surat','sk.tujuan as pihak','sk.perihal','sk.status','a.archived_at'])
            ->get()->map(function($r){ $r->jenis='keluar'; return $r; });

        if ($jenis === 'masuk') {
            $rekapUnarchived = $smUnarchived; $rekapArchived = $smArchived;
        } elseif ($jenis === 'keluar') {
            $rekapUnarchived = $skUnarchived; $rekapArchived = $skArchived;
        } else {
            $rekapUnarchived = $smUnarchived->concat($skUnarchived);
            $rekapArchived = $smArchived->concat($skArchived);
        }

        $totalMasuk = DB::table('surat_masuk')->whereBetween('tanggal_terima', [$from, $to])->count();
        $totalKeluar = DB::table('surat_keluar')->whereBetween('tanggal_surat', [$from, $to])->count();
        $totalArsip = DB::table('arsip')->whereBetween('archived_at', [$from.' 00:00:00', $to.' 23:59:59'])->count();
        $totalDiproses = $totalMasuk + $totalKeluar;

        $data = [
            'filters' => ['from_date'=>$from,'to_date'=>$to,'jenis'=>$jenis],
            'summary' => [
                'total_masuk'=>$totalMasuk,
                'total_keluar'=>$totalKeluar,
                'total_arsip'=>$totalArsip,
                'total_diproses'=>$totalDiproses,
            ],
            'rekap_unarchived' => $rekapUnarchived->values(),
            'rekap_archived' => $rekapArchived->values(),
        ];

        // Render PDF using dompdf
        $pdf = PDF::loadView('reports.pdf', $data)->setPaper('a4', 'portrait');
        $title = 'laporan-'.($jenis==='semua'?'rekap':$jenis).'-'.$from.'-sd-'.$to.'.pdf';
        return $pdf->download($title);
    }

    public function exportExcel(Request $request)
    {
        $from = $request->get('from_date');
        $to = $request->get('to_date');
        $jenis = strtolower((string) $request->get('jenis')) ?: 'semua';
        if (!$from || !$to) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
            $from = $from ?: $start->toDateString();
            $to = $to ?: $end->toDateString();
        }

        // Build datasets (same as index/exportPdf)
        $smUnarchived = DB::table('surat_masuk as sm')
            ->leftJoin('arsip as a', function ($j) { $j->on('a.surat_id', '=', 'sm.id')->where('a.jenis_surat', 'masuk'); })
            ->whereBetween('sm.tanggal_terima', [$from, $to])
            ->whereNull('a.id')
            ->select(['sm.id as surat_id','sm.nomor_surat','sm.tanggal_terima as tanggal_surat','sm.pengirim as pihak','sm.perihal','sm.status', DB::raw('NULL as archived_at')])
            ->get()->map(function($r){ $r->jenis='masuk'; return $r; });
        $smArchived = DB::table('surat_masuk as sm')
            ->join('arsip as a', function ($j) { $j->on('a.surat_id', '=', 'sm.id')->where('a.jenis_surat', 'masuk'); })
            ->whereBetween('a.archived_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->select(['sm.id as surat_id','sm.nomor_surat','sm.tanggal_terima as tanggal_surat','sm.pengirim as pihak','sm.perihal','sm.status','a.archived_at'])
            ->get()->map(function($r){ $r->jenis='masuk'; return $r; });
        $skUnarchived = DB::table('surat_keluar as sk')
            ->leftJoin('arsip as a', function ($j) { $j->on('a.surat_id', '=', 'sk.id')->where('a.jenis_surat', 'keluar'); })
            ->whereBetween('sk.tanggal_surat', [$from, $to])
            ->whereNull('a.id')
            ->select(['sk.id as surat_id','sk.nomor_surat','sk.tanggal_surat','sk.tujuan as pihak','sk.perihal','sk.status', DB::raw('NULL as archived_at')])
            ->get()->map(function($r){ $r->jenis='keluar'; return $r; });
        $skArchived = DB::table('surat_keluar as sk')
            ->join('arsip as a', function ($j) { $j->on('a.surat_id', '=', 'sk.id')->where('a.jenis_surat', 'keluar'); })
            ->whereBetween('a.archived_at', [$from.' 00:00:00', $to.' 23:59:59'])
            ->select(['sk.id as surat_id','sk.nomor_surat','sk.tanggal_surat','sk.tujuan as pihak','sk.perihal','sk.status','a.archived_at'])
            ->get()->map(function($r){ $r->jenis='keluar'; return $r; });

        if ($jenis === 'masuk') {
            $rows = $smUnarchived->concat($smArchived);
        } elseif ($jenis === 'keluar') {
            $rows = $skUnarchived->concat($skArchived);
        } else {
            $rows = $smUnarchived->concat($skUnarchived)->concat($smArchived)->concat($skArchived);
        }

        // Build lampiran URL map keyed by "jenis:surat_id"
        $idsMasuk = $rows->where('jenis','masuk')->pluck('surat_id')->unique()->values();
        $idsKeluar = $rows->where('jenis','keluar')->pluck('surat_id')->unique()->values();
        $lampiranMap = [];
        if ($idsMasuk->count() > 0) {
            $lamMasuk = DB::table('lampiran')->whereIn('surat_masuk_id', $idsMasuk)->get();
            foreach ($lamMasuk as $l) {
                $key = 'masuk:'.$l->surat_masuk_id;
                $lampiranMap[$key] = $lampiranMap[$key] ?? [];
                $lampiranMap[$key][] = url(Storage::url($l->file_path));
            }
        }
        if ($idsKeluar->count() > 0) {
            $lamKeluar = DB::table('lampiran')->whereIn('surat_keluar_id', $idsKeluar)->get();
            foreach ($lamKeluar as $l) {
                $key = 'keluar:'.$l->surat_keluar_id;
                $lampiranMap[$key] = $lampiranMap[$key] ?? [];
                $lampiranMap[$key][] = url(Storage::url($l->file_path));
            }
        }

        $filename = 'laporan-'.($jenis==='semua'?'rekap':$jenis).'-'.$from.'-sd-'.$to.'.xlsx';
        return Excel::download(new ReportExport($rows, $lampiranMap), $filename);
    }
}
