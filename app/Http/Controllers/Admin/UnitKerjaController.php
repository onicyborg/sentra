<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UnitKerjaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('can:user.manage');
    }

    public function index()
    {
        $items = UnitKerja::orderBy('name')->get(['id', 'name']);

        return view('unit-kerja.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:190', 'unique:unit_kerja,name'],
        ]);

        UnitKerja::create([
            'name' => $validated['name'],
        ]);

        return back()->with('success', 'Unit kerja created');
    }

    public function show(string $id)
    {
        $uk = UnitKerja::findOrFail($id);
        return response()->json([
            'id' => $uk->id,
            'name' => $uk->name,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $uk = UnitKerja::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:190', Rule::unique('unit_kerja', 'name')->ignore($uk->id)],
        ]);

        $uk->name = $validated['name'];
        $uk->save();

        return back()->with('success', 'Unit kerja updated');
    }

    public function destroy(string $id)
    {
        $uk = UnitKerja::findOrFail($id);

        $hasUsers = User::query()->where('unit_kerja_id', $uk->id)->exists();
        $hasCurrentSuratMasuk = DB::table('surat_masuk')->where('current_unit_kerja_id', $uk->id)->exists();

        if ($hasUsers || $hasCurrentSuratMasuk) {
            return back()->with('error', 'Unit kerja tidak dapat dihapus karena masih digunakan.');
        }

        $uk->delete();

        return back()->with('success', 'Unit kerja deleted');
    }
}
