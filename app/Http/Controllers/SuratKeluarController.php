<?php

namespace App\Http\Controllers;

use App\Models\Lampiran;
use App\Models\SuratKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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

    public function index()
    {
        $q = SuratKeluar::query()->latest('created_at');
        if (auth()->user()?->can('surat_keluar.approve')) {
            $q->whereIn('status', ['draft', 'ditolak']);
        }
        $items = $q->get(['id','nomor_surat','tanggal_surat','tujuan','perihal','status']);

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

        $sk->status = 'terkirim';
        // Catat tanggal_kirim & media bila ada kolomnya; jika belum ada, bisa di-log/abaikan
        $sk->save();

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

        return back()->with('success', $validated['aksi'] === 'approve' ? 'Surat keluar disahkan' : 'Catatan penolakan tersimpan');
    }
}
