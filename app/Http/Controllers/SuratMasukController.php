<?php

namespace App\Http\Controllers;

use App\Models\Disposisi;
use App\Models\Lampiran;
use App\Models\SuratMasuk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\NotificationService;

class SuratMasukController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('can:surat_masuk.read')->only(['index', 'show']);
        // Use create permission as edit capability per spec
        $this->middleware('can:surat_masuk.create')->only(['store', 'update']);
        // Kepala Dinas capabilities
        $this->middleware('can:surat_masuk.verify')->only(['verify']);
        $this->middleware('can:surat_masuk.distribute')->only(['distribute']);
    }

    public function index()
    {
        $q = SuratMasuk::query()
            ->leftJoin('arsip', function ($join) {
                $join->on('arsip.surat_id', '=', 'surat_masuk.id')
                     ->where('arsip.jenis_surat', '=', 'masuk');
            })
            ->whereNull('arsip.id');
        // Untuk Kepala Dinas: tampilkan hanya diterima/terverifikasi
        if (auth()->user()?->can('surat_masuk.verify') || auth()->user()?->can('surat_masuk.distribute')) {
            $q->whereIn('status', ['diterima', 'terverifikasi']);
        }
        $q->orderByRaw("CASE status
            WHEN 'draft' THEN 1
            WHEN 'diterima' THEN 2
            WHEN 'terverifikasi' THEN 3
            WHEN 'didisposisikan' THEN 4
            WHEN 'ditindaklanjuti' THEN 5
            ELSE 6 END")
          ->orderByDesc('surat_masuk.created_at');
        $items = $q->get(['surat_masuk.id','nomor_surat','tanggal_terima','asal_surat','pengirim','perihal','status']);

        return view('surat-masuk.index', [
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => ['required', 'string', 'max:190'],
            'tanggal_terima' => ['required', 'date'],
            'asal_surat' => ['nullable', 'string', 'max:190'],
            'pengirim' => ['nullable', 'string', 'max:190'],
            'perihal' => ['required', 'string', 'max:255'],
            'lampiran' => ['nullable', 'array'],
            'lampiran.*' => ['file'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $sm = new SuratMasuk();
            $sm->nomor_surat = $validated['nomor_surat'];
            $sm->tanggal_terima = $validated['tanggal_terima'];
            $sm->asal_surat = $validated['asal_surat'] ?? null;
            $sm->pengirim = $validated['pengirim'] ?? null;
            $sm->perihal = $validated['perihal'];
            $sm->status = $request->input('status', 'draft');
            $sm->created_by = Auth::id();
            $sm->save();

            if ($request->hasFile('lampiran')) {
                foreach ((array) $request->file('lampiran') as $file) {
                    if (!$file) continue;
                    $path = $file->store('lampiran/surat_masuk', 'public');
                    Lampiran::create([
                        'surat_masuk_id' => $sm->id,
                        'file_path' => $path,
                    ]);
                }
            }

            // Event: surat_masuk.created -> to permission surat_masuk.verify
            try {
                app(NotificationService::class)->sendToPermission(
                    'surat_masuk.verify',
                    'Surat Masuk Baru',
                    'Surat masuk baru: ' . ($sm->nomor_surat ?? '-') . ' - ' . $sm->perihal
                );
            } catch (\Throwable $e) { /* handled by global logs if any */ }
        });

        return back()->with('success', 'Surat masuk berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $sm = SuratMasuk::findOrFail($id);
        $lampiran = $sm->lampiran()
            ->where('file_path', 'like', 'lampiran/surat_masuk/%')
            ->get(['id','file_path']);

        return response()->json([
            'id' => $sm->id,
            'nomor_surat' => $sm->nomor_surat,
            'tanggal_terima' => $sm->tanggal_terima,
            'asal_surat' => $sm->asal_surat,
            'pengirim' => $sm->pengirim,
            'perihal' => $sm->perihal,
            'status' => $sm->status,
            'editable' => in_array($sm->status, ['draft','diterima']),
            'lampiran' => $lampiran->map(function ($l) {
                return [
                    'id' => $l->id,
                    // Normalisasi: ambil hanya path dari hasil Storage::url lalu prefix dengan host saat ini
                    'url' => (function () use ($l) {
                        $raw = Storage::disk('public')->url($l->file_path);
                        $path = parse_url($raw, PHP_URL_PATH) ?? $raw;
                        return request()->getSchemeAndHttpHost() . $path;
                    })(),
                    'name' => basename($l->file_path),
                ];
            }),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $sm = SuratMasuk::findOrFail($id);
        if (!in_array($sm->status, ['draft','diterima'])) {
            return back()->with('error', 'Surat tidak dapat diedit pada status saat ini.');
        }

        $validated = $request->validate([
            'nomor_surat' => ['required', 'string', 'max:190'],
            'tanggal_terima' => ['required', 'date'],
            'asal_surat' => ['nullable', 'string', 'max:190'],
            'pengirim' => ['nullable', 'string', 'max:190'],
            'perihal' => ['required', 'string', 'max:255'],
            'lampiran' => ['nullable', 'array'],
            'lampiran.*' => ['file'],
        ]);

        DB::transaction(function () use ($validated, $request, $sm) {
            $sm->nomor_surat = $validated['nomor_surat'];
            $sm->tanggal_terima = $validated['tanggal_terima'];
            $sm->asal_surat = $validated['asal_surat'] ?? null;
            $sm->pengirim = $validated['pengirim'] ?? null;
            $sm->perihal = $validated['perihal'];
            $sm->status = $request->input('status', $sm->status);
            $sm->save();

            if ($request->hasFile('lampiran')) {
                // Tambah lampiran baru (tidak menghapus yang lama)
                foreach ((array) $request->file('lampiran') as $file) {
                    if (!$file) continue;
                    $path = $file->store('lampiran/surat_masuk', 'public');
                    Lampiran::create([
                        'surat_masuk_id' => $sm->id,
                        'file_path' => $path,
                    ]);
                }
            }
        });

        return back()->with('success', 'Surat masuk berhasil diperbarui');
    }

    public function verify(Request $request, string $id)
    {
        $sm = SuratMasuk::findOrFail($id);
        // Hanya boleh verifikasi jika status 'diterima'
        if ($sm->status !== 'diterima') {
            return back()->with('error', 'Surat tidak dapat diverifikasi pada status saat ini.');
        }

        $validated = $request->validate([
            'catatan' => ['nullable','string','max:1000'],
        ]);

        DB::transaction(function () use ($sm) {
            $sm->status = 'terverifikasi';
            $sm->save();
            // Activity log assumed handled globally if enabled
            // Event: surat_masuk.verified -> to created_by
            try {
                if ($sm->created_by) {
                    app(NotificationService::class)->sendToUser(
                        $sm->created_by,
                        'Surat Masuk Terverifikasi',
                        'Surat masuk ' . ($sm->nomor_surat ?? '-') . ' telah diverifikasi.'
                    );
                }
            } catch (\Throwable $e) { }
        });

        return back()->with('success', 'Surat masuk berhasil diverifikasi');
    }

    public function distribute(Request $request, string $id)
    {
        $sm = SuratMasuk::findOrFail($id);
        // Hanya boleh disposisi jika sudah terverifikasi
        if ($sm->status !== 'terverifikasi') {
            return back()->with('error', 'Surat belum terverifikasi, tidak dapat didisposisikan.');
        }

        $validated = $request->validate([
            'ke_unit' => ['required','string','max:190'],
            'catatan' => ['nullable','string','max:1000'],
        ]);

        DB::transaction(function () use ($validated, $sm) {
            Disposisi::create([
                'surat_masuk_id' => $sm->id,
                'dari_user' => auth()->id(),
                'ke_unit' => $validated['ke_unit'],
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'baru',
            ]);
            $sm->status = 'didisposisikan';
            $sm->save();

            // Event: surat_masuk.distributed -> to permission surat_masuk.follow_up
            try {
                app(NotificationService::class)->sendToPermission(
                    'surat_masuk.follow_up',
                    'Surat Masuk Didisposisikan',
                    'Surat masuk ' . ($sm->nomor_surat ?? '-') . ' membutuhkan tindak lanjut.'
                );
            } catch (\Throwable $e) { }
        });

        return back()->with('success', 'Surat masuk berhasil didisposisikan');
    }
}
