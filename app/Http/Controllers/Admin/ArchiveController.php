<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Arsip;
use App\Models\SuratMasuk;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        // index & show require archive.read; restore & destroy require archive.manage
        $this->middleware('can:archive.read')->only(['index', 'show']);
        $this->middleware('can:archive.manage')->only(['restore', 'destroy']);
    }

    public function index(Request $request)
    {
        $rows = Arsip::query()
            ->leftJoin('surat_masuk as sm', function ($j) {
                $j->on('arsip.surat_id', '=', 'sm.id')->where('arsip.jenis_surat', 'masuk');
            })
            ->leftJoin('surat_keluar as sk', function ($j) {
                $j->on('arsip.surat_id', '=', 'sk.id')->where('arsip.jenis_surat', 'keluar');
            })
            ->orderByDesc('arsip.archived_at')
            ->get([
                'arsip.id',
                'arsip.jenis_surat',
                'arsip.archived_at',
                'sm.nomor_surat as sm_nomor_surat',
                'sk.nomor_surat as sk_nomor_surat',
                'sm.perihal as sm_perihal',
                'sk.perihal as sk_perihal',
                'sm.tanggal_terima as sm_tanggal_surat',
                'sk.tanggal_surat as sk_tanggal_surat',
                'sm.pengirim as sm_pihak',
                'sk.tujuan as sk_pihak',
            ])
            ->map(function ($r) {
                $r->nomor_surat = $r->sm_nomor_surat ?? $r->sk_nomor_surat;
                $r->perihal = $r->sm_perihal ?? $r->sk_perihal;
                $r->tanggal_surat = $r->sm_tanggal_surat ?? $r->sk_tanggal_surat;
                $r->pihak = $r->sm_pihak ?? $r->sk_pihak;
                return $r;
            });

        return view('arsip.index', [
            'arsip' => $rows,
            'filters' => [
                'jenis' => (string) $request->get('jenis', ''),
                'nomor' => (string) $request->get('nomor', ''),
                'from' => (string) $request->get('from', ''),
                'to' => (string) $request->get('to', ''),
                'pengirim' => (string) $request->get('pengirim', ''),
                'tujuan' => (string) $request->get('tujuan', ''),
                'perihal' => (string) $request->get('perihal', ''),
            ],
        ]);
    }

    public function search(Request $request)
    {
        $jenis = strtolower((string) $request->get('jenis'));
        $nomor = trim((string) $request->get('nomor')) ?: null;
        $from = $request->get('from');
        $to = $request->get('to');
        $pengirim = trim((string) $request->get('pengirim')) ?: null;
        $tujuan = trim((string) $request->get('tujuan')) ?: null;
        $perihal = trim((string) $request->get('perihal')) ?: null;

        $q = Arsip::query()
            ->whereNotNull('archived_at')
            // Selalu join keduanya agar COALESCE pada select tidak error alias
            ->leftJoin('surat_masuk as sm', function ($j) {
                $j->on('arsip.surat_id', '=', 'sm.id')
                  ->where('arsip.jenis_surat', 'masuk');
            })
            ->leftJoin('surat_keluar as sk', function ($j) {
                $j->on('arsip.surat_id', '=', 'sk.id')
                  ->where('arsip.jenis_surat', 'keluar');
            });

        // Jika pengguna memilih jenis tertentu, batasi hasilnya
        if ($jenis === 'masuk') {
            $q->where('arsip.jenis_surat', 'masuk');
        } elseif ($jenis === 'keluar') {
            $q->where('arsip.jenis_surat', 'keluar');
        }

        if ($nomor) {
            $q->where(function ($w) use ($nomor) {
                $w->where('sm.nomor_surat', 'like', "%$nomor%")
                  ->orWhere('sk.nomor_surat', 'like', "%$nomor%");
            });
        }
        if ($perihal) {
            $q->where(function ($w) use ($perihal) {
                $w->where('sm.perihal', 'like', "%$perihal%")
                  ->orWhere('sk.perihal', 'like', "%$perihal%");
            });
        }
        if ($from) {
            $q->where(function ($w) use ($from) {
                $w->whereDate('sm.tanggal_terima', '>=', $from)
                  ->orWhereDate('sk.tanggal_surat', '>=', $from);
            });
        }
        if ($to) {
            $q->where(function ($w) use ($to) {
                $w->whereDate('sm.tanggal_terima', '<=', $to)
                  ->orWhereDate('sk.tanggal_surat', '<=', $to);
            });
        }
        if ($pengirim) {
            $q->where('arsip.jenis_surat', 'masuk')->where('sm.pengirim', 'like', "%$pengirim%");
        }
        if ($tujuan) {
            $q->where('arsip.jenis_surat', 'keluar')->where('sk.tujuan', 'like', "%$tujuan%");
        }

        $rows = $q->orderByDesc('arsip.archived_at')->get([
            'arsip.id',
            'arsip.jenis_surat',
            'arsip.archived_at',
            'sm.nomor_surat as sm_nomor_surat',
            'sk.nomor_surat as sk_nomor_surat',
            'sm.perihal as sm_perihal',
            'sk.perihal as sk_perihal',
            'sm.tanggal_terima as sm_tanggal_surat',
            'sk.tanggal_surat as sk_tanggal_surat',
            'sm.pengirim as sm_pihak',
            'sk.tujuan as sk_pihak',
        ])->map(function ($r) {
            $r->nomor_surat = $r->sm_nomor_surat ?? $r->sk_nomor_surat;
            $r->perihal = $r->sm_perihal ?? $r->sk_perihal;
            $r->tanggal_surat = $r->sm_tanggal_surat ?? $r->sk_tanggal_surat;
            $r->pihak = $r->sm_pihak ?? $r->sk_pihak;
            return $r;
        });

        return view('arsip.index', [
            'arsip' => $rows,
            'filters' => [
                'jenis' => $jenis ?: '',
                'nomor' => $nomor ?: '',
                'from' => $from ?: '',
                'to' => $to ?: '',
                'pengirim' => $pengirim ?: '',
                'tujuan' => $tujuan ?: '',
                'perihal' => $perihal ?: '',
            ],
        ]);
    }

    public function show(string $id)
    {
        $row = Arsip::findOrFail($id);
        if ($row->jenis_surat === 'masuk') {
            $surat = SuratMasuk::find($row->surat_id);
            $payload = [
                'id' => $row->id,
                'jenis_surat' => 'masuk',
                'surat_id' => $row->surat_id,
                'nomor_surat' => $surat->nomor_surat ?? null,
                'perihal' => $surat->perihal ?? null,
                'tanggal_surat' => $surat->tanggal_terima ?? null,
                'archived_at' => $row->archived_at,
            ];
        } else {
            $surat = SuratKeluar::find($row->surat_id);
            $payload = [
                'id' => $row->id,
                'jenis_surat' => 'keluar',
                'surat_id' => $row->surat_id,
                'nomor_surat' => $surat->nomor_surat ?? null,
                'perihal' => $surat->perihal ?? null,
                'tanggal_surat' => $surat->tanggal_surat ?? null,
                'archived_at' => $row->archived_at,
            ];
        }
        return response()->json($payload);
    }

    public function restore(string $id)
    {
        DB::transaction(function () use ($id) {
            $row = Arsip::findOrFail($id);
            // Optional: update status surat back to aktif if needed (not specified)
            $row->delete();
        });

        return back()->with('success', 'Arsip berhasil direstore.');
    }

    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $row = Arsip::findOrFail($id);
            $row->delete();
        });

        return back()->with('success', 'Arsip berhasil dihapus.');
    }
}
