<?php

namespace App\Http\Controllers;

use App\Models\Lampiran;
use App\Models\SuratMasuk;
use App\Models\Disposisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $items = SuratMasuk::query()
            ->whereIn('status', ['didisposisikan', 'ditindaklanjuti'])
            ->latest('created_at')
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

        $validated = $request->validate([
            'deskripsi' => ['required','string'],
            'unit' => ['required','string'],
            'lampiran' => ['nullable','array'],
            'lampiran.*' => ['file'],
        ]);

        DB::transaction(function () use ($validated, $request, $sm) {
            // Simpan tindak lanjut (tabel tindak_lanjut)
            DB::table('tindak_lanjut')->insert([
                'id' => (string) \Str::uuid(),
                'surat_masuk_id' => $sm->id,
                'unit' => $validated['unit'],
                'deskripsi' => $validated['deskripsi'],
                'created_at' => now(),
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
}
