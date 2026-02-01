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
                DB::raw('COALESCE(sm.nomor_surat, sk.nomor_surat) as nomor_surat'),
                DB::raw('COALESCE(sm.perihal, sk.perihal) as perihal'),
                DB::raw('COALESCE(sm.tanggal_terima, sk.tanggal_surat) as tanggal_surat'),
            ]);

        return view('arsip.index', [
            'arsip' => $rows,
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
