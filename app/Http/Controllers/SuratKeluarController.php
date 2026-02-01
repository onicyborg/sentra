<?php

namespace App\Http\Controllers;

use App\Models\Lampiran;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\NotificationService;
use Illuminate\Support\Str;

class SuratKeluarController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('can:surat_keluar.read')->only(['index', 'show']);
        $this->middleware('can:surat_keluar.create')->only(['store', 'update']);
        $this->middleware('can:surat_keluar.send')->only(['send']);
        $this->middleware('can:surat_keluar.approve')->only(['approve']);
    }

    private function autoArchiveSuratKeluar(SuratKeluar $sk): void
    {
        // Hindari duplikasi arsip
        $exists = DB::table('arsip')
            ->where('jenis_surat', 'keluar')
            ->where('surat_id', $sk->id)
            ->exists();
        if ($exists) return;

        DB::table('arsip')->insert([
            'id' => (string) Str::uuid(),
            'jenis_surat' => 'keluar',
            'surat_id' => $sk->id,
            'archived_at' => now(),
        ]);
    }

    public function index()
    {
        $q = SuratKeluar::query()
            ->leftJoin('arsip', function ($join) {
                $join->on('arsip.surat_id', '=', 'surat_keluar.id')
                     ->where('arsip.jenis_surat', '=', 'keluar');
            })
            ->whereNull('arsip.id');
        if (auth()->user()?->can('surat_keluar.approve')) {
            $q->whereIn('status', ['draft', 'ditolak']);
        }
        $items = $q->get(['surat_keluar.id','nomor_surat','tanggal_surat','tujuan','perihal','status','surat_keluar.created_at']);

        // Order in PHP to avoid DB-specific raw CASE
        $priority = [
            'draft' => 1,
            'disahkan' => 2,
            'terkirim' => 3,
        ];
        $items = $items->sort(function ($a, $b) use ($priority) {
            $pa = $priority[$a->status] ?? 99;
            $pb = $priority[$b->status] ?? 99;
            if ($pa === $pb) {
                return ($b->created_at <=> $a->created_at);
            }
            return $pa <=> $pb;
        })->values();

        return view('surat-keluar.index', [
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_surat' => ['required','string','max:190'],
            'tanggal_surat' => ['required','date'],
            'tujuan' => ['required','string','max:190'],
            'perihal' => ['required','string','max:255'],
            'lampiran' => ['nullable','array'],
            'lampiran.*' => ['file','mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png'],
        ]);

        DB::transaction(function () use ($validated, $request) {
            $sk = new SuratKeluar();
            $sk->nomor_surat = $validated['nomor_surat'];
            $sk->tanggal_surat = $validated['tanggal_surat'];
            $sk->tujuan = $validated['tujuan'];
            $sk->perihal = $validated['perihal'];
            $sk->status = 'draft';
            $sk->created_by = Auth::id();
            $sk->save();

            if ($request->hasFile('lampiran')) {
                foreach ((array) $request->file('lampiran') as $file) {
                    if (!$file) continue;
                    $path = $file->store('lampiran/surat_keluar', 'public');
                    Lampiran::create([
                        'surat_keluar_id' => $sk->id,
                        'file_path' => $path,
                    ]);
                }
            }

            // Event: surat_keluar.created -> to permission surat_keluar.approve
            try {
                app(NotificationService::class)->sendToPermission(
                    'surat_keluar.approve',
                    'Surat Keluar Baru',
                    'Surat keluar baru: ' . ($sk->nomor_surat ?? '-') . ' - ' . $sk->perihal
                );
            } catch (\Throwable $e) { }
        });

        return back()->with('success', 'Surat keluar berhasil ditambahkan');
    }

    public function show(string $id)
    {
        $sk = SuratKeluar::findOrFail($id);
        $lampiran = $sk->lampiran()->get(['id','file_path']);

        return response()->json([
            'id' => $sk->id,
            'nomor_surat' => $sk->nomor_surat,
            'tanggal_surat' => $sk->tanggal_surat,
            'tujuan' => $sk->tujuan,
            'perihal' => $sk->perihal,
            'status' => $sk->status,
            'editable' => in_array($sk->status, ['draft','ditolak']),
            'sendable' => ($sk->status === 'disahkan'),
            'lampiran' => $lampiran->map(function ($l) {
                $raw = Storage::disk('public')->url($l->file_path);
                $path = parse_url($raw, PHP_URL_PATH) ?? $raw;
                return [
                    'id' => $l->id,
                    'url' => request()->getSchemeAndHttpHost() . $path,
                    'name' => basename($l->file_path),
                ];
            }),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $sk = SuratKeluar::findOrFail($id);
        if (!in_array($sk->status, ['draft', 'ditolak'])) {
            return back()->with('error', 'Surat tidak dapat diedit pada status saat ini.');
        }

        $validated = $request->validate([
            'nomor_surat' => ['required','string','max:190'],
            'tanggal_surat' => ['required','date'],
            'tujuan' => ['required','string','max:190'],
            'perihal' => ['required','string','max:255'],
            'lampiran' => ['nullable','array'],
            'lampiran.*' => ['file','mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/jpeg,image/png'],
        ]);

        DB::transaction(function () use ($validated, $request, $sk) {
            $sk->nomor_surat = $validated['nomor_surat'];
            $sk->tanggal_surat = $validated['tanggal_surat'];
            $sk->tujuan = $validated['tujuan'];
            $sk->perihal = $validated['perihal'];
            $sk->status = 'draft';
            $sk->save();

            if ($request->hasFile('lampiran')) {
                foreach ((array) $request->file('lampiran') as $file) {
                    if (!$file) continue;
                    $path = $file->store('lampiran/surat_keluar', 'public');
                    Lampiran::create([
                        'surat_keluar_id' => $sk->id,
                        'file_path' => $path,
                    ]);
                }
            }
        });

        return back()->with('success', 'Surat keluar berhasil diperbarui');
    }

    public function send(Request $request, string $id)
    {
        $sk = SuratKeluar::findOrFail($id);
        if ($sk->status !== 'disahkan') {
            return back()->with('error', 'Surat belum disahkan, tidak dapat dikirim.');
        }

        $validated = $request->validate([
            'tanggal_kirim' => ['required','date'],
            'media_pengiriman' => ['nullable','string','max:190'],
        ]);

        DB::transaction(function () use ($sk) {
            $sk->status = 'terkirim';
            // Catat tanggal_kirim & media bila ada kolomnya; jika belum ada, bisa di-log/abaikan
            $sk->save();

            // Auto-archive Surat Keluar (status final)
            $this->autoArchiveSuratKeluar($sk);
        });

        // Event: surat_keluar.sent -> to created_by and permission surat_keluar.approve
        try {
            if ($sk->created_by) {
                app(NotificationService::class)->sendToUser(
                    $sk->created_by,
                    'Surat Keluar Terkirim',
                    'Surat keluar ' . ($sk->nomor_surat ?? '-') . ' telah dikirim.'
                );
            }
            app(NotificationService::class)->sendToPermission(
                'surat_keluar.approve',
                'Surat Keluar Terkirim',
                'Surat keluar ' . ($sk->nomor_surat ?? '-') . ' telah dikirim.'
            );
        } catch (\Throwable $e) { }

        return back()->with('success', 'Surat keluar berhasil dikirim');
    }

    public function approve(Request $request, string $id)
    {
        $sk = SuratKeluar::findOrFail($id);
        if (!in_array($sk->status, ['draft', 'ditolak'])) {
            return back()->with('error', 'Surat tidak dapat di-approve pada status saat ini.');
        }

        $validated = $request->validate([
            'aksi' => ['required','in:approve,reject'],
            'catatan' => ['nullable','string','max:1000'],
        ]);

        DB::transaction(function () use ($validated, $sk) {
            if ($validated['aksi'] === 'approve') {
                $sk->status = 'disahkan';
                $sk->approved_by = Auth::id();
            } else {
                $sk->status = 'ditolak';
                $sk->approved_by = Auth::id();
            }
            $sk->save();
        });

        // Event: surat_keluar.approved/rejected -> to created_by
        try {
            if ($sk->created_by) {
                app(NotificationService::class)->sendToUser(
                    $sk->created_by,
                    $validated['aksi'] === 'approve' ? 'Surat Keluar Disahkan' : 'Surat Keluar Ditolak',
                    'Surat keluar ' . ($sk->nomor_surat ?? '-') . ($validated['aksi'] === 'approve' ? ' telah disahkan.' : ' ditolak.')
                );
            }
        } catch (\Throwable $e) { }

        return back()->with('success', $validated['aksi'] === 'approve' ? 'Surat keluar disahkan' : 'Catatan penolakan tersimpan');
    }
}
