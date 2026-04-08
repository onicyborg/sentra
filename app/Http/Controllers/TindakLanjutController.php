<?php

namespace App\Http\Controllers;

use App\Models\Lampiran;
use App\Models\SuratMasuk;
use App\Models\Disposisi;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\NotificationService;

class TindakLanjutController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('can:surat_masuk.follow_up')->only(['index', 'store']);
    }

    public function index()
    {
        // Ambil semua surat masuk dengan status didisposisikan atau ditindaklanjuti
        $q = SuratMasuk::query()
            ->whereIn('status', ['didisposisikan', 'ditindaklanjuti']);

        if (auth()->user()?->roles()->where('name', 'unit_kerja')->exists()) {
            $unitKerjaId = auth()->user()->unit_kerja_id;
            if ($unitKerjaId) {
                $q->where('current_unit_kerja_id', $unitKerjaId);
            } else {
                $q->whereRaw('1 = 0');
            }
        }

        $items = $q->latest('created_at')
            ->get(['id','nomor_surat','tanggal_terima','asal_surat','perihal','status']);

        // Ambil catatan disposisi terakhir per surat
        $notes = Disposisi::query()
            ->select('surat_masuk_id','catatan','created_at')
            ->orderBy('created_at','desc')
            ->get()
            ->unique('surat_masuk_id')
            ->keyBy('surat_masuk_id');

        return view('tindak-lanjut.index', [
            'items' => $items,
            'notes' => $notes,
        ]);
    }

    public function store(Request $request, string $suratMasukId)
    {
        $sm = SuratMasuk::findOrFail($suratMasukId);
        if (!in_array($sm->status, ['didisposisikan'])) {
            return back()->with('error', 'Surat tidak dapat ditindaklanjuti pada status saat ini.');
        }

        if (auth()->user()?->roles()->where('name', 'unit_kerja')->exists()) {
            $unitKerjaId = auth()->user()->unit_kerja_id;
            if (! $unitKerjaId || $sm->current_unit_kerja_id !== $unitKerjaId) {
                return back()->with('error', 'Anda tidak memiliki akses ke surat ini.');
            }
        }

        $validated = $request->validate([
            'deskripsi' => ['required','string'],
            'lampiran' => ['nullable','array'],
            'lampiran.*' => ['file'],
        ]);

        DB::transaction(function () use ($validated, $request, $sm) {
            $latestDisposisi = Disposisi::query()
                ->where('surat_masuk_id', $sm->id)
                ->orderByDesc('created_at')
                ->first();

            $unitName = null;
            if ($latestDisposisi?->unit_kerja_id) {
                $unitName = UnitKerja::where('id', $latestDisposisi->unit_kerja_id)->value('name');
            }
            $unitName = $unitName ?: ($latestDisposisi->ke_unit ?? null);

            // Simpan tindak lanjut (tabel tindak_lanjut)
            DB::table('tindak_lanjut')->insert([
                'id' => (string) Str::uuid(),
                'surat_masuk_id' => $sm->id,
                'unit' => $unitName,
                'deskripsi' => $validated['deskripsi'],
            ]);

            // Simpan lampiran (opsional) ke tabel lampiran dengan surat_masuk_id
            if ($request->hasFile('lampiran')) {
                foreach ((array) $request->file('lampiran') as $file) {
                    if (!$file) continue;
                    $path = $file->store('lampiran/tindak_lanjut', 'public');
                    Lampiran::create([
                        'surat_masuk_id' => $sm->id,
                        'file_path' => $path,
                    ]);
                }
            }

            // Update status surat
            $sm->status = 'ditindaklanjuti';
            $sm->save();

            // Auto-archive Surat Masuk (status final)
            $this->autoArchiveSuratMasuk($sm);

            // Event: surat_masuk.followed_up
            try {
                if ($sm->created_by) {
                    app(NotificationService::class)->sendToUser(
                        $sm->created_by,
                        'Surat Masuk Ditindaklanjuti',
                        'Surat masuk ' . ($sm->nomor_surat ?? '-') . ' telah ditindaklanjuti.'
                    );
                }
                app(NotificationService::class)->sendToPermission(
                    'surat_masuk.verify',
                    'Surat Masuk Ditindaklanjuti',
                    'Surat masuk ' . ($sm->nomor_surat ?? '-') . ' telah ditindaklanjuti.'
                );
            } catch (\Throwable $e) { }
        });

        return back()->with('success', 'Tindak lanjut berhasil disimpan');
    }

    private function autoArchiveSuratMasuk(SuratMasuk $sm): void
    {
        // Hindari duplikasi arsip
        $exists = DB::table('arsip')
            ->where('jenis_surat', 'masuk')
            ->where('surat_id', $sm->id)
            ->exists();
        if ($exists) return;

        DB::table('arsip')->insert([
            'id' => (string) Str::uuid(),
            'jenis_surat' => 'masuk',
            'surat_id' => $sm->id,
            'archived_at' => now(),
        ]);
    }
}
