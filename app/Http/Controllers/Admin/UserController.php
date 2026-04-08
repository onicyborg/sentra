<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Models\UnitKerja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('can:user.manage');
    }

    public function index()
    {
        $users = User::with(['roles', 'unitKerja'])->orderBy('name')->get();
        $roles = Roles::orderBy('name')->get();
        $unitKerja = UnitKerja::orderBy('name')->get(['id', 'name']);

        return view('users.index', compact('users', 'roles', 'unitKerja'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role_id' => ['nullable', 'uuid', Rule::exists('roles', 'id')],
            'unit_kerja_id' => ['nullable', 'uuid', Rule::exists('unit_kerja', 'id')],
        ]);

        $roleId = $validated['role_id'] ?? null;
        $roleName = $roleId ? Roles::where('id', $roleId)->value('name') : null;
        if ($roleName === 'unit_kerja' && empty($validated['unit_kerja_id'])) {
            return redirect()->back()->with('error', 'Unit kerja wajib diisi untuk role Unit Kerja.');
        }

        $user = new User();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = $validated['password']; // hashed by cast
        $user->save();

        // Assign a single role if provided
        $user->roles()->sync($roleId ? [$roleId] : []);
        if ($roleName === 'unit_kerja') {
            $user->unit_kerja_id = $validated['unit_kerja_id'];
            $user->save();
        } else {
            $user->unit_kerja_id = null;
            $user->save();
        }

        return redirect()->back()->with('success', 'User created');
    }

    public function show(string $id)
    {
        $user = User::with('roles:id,name')->findOrFail($id);
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'unit_kerja_id' => $user->unit_kerja_id,
            'roles' => $user->roles->map(fn($r) => ['id' => $r->id, 'name' => $r->name]),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role_id' => ['nullable', 'uuid', Rule::exists('roles', 'id')],
            'unit_kerja_id' => ['nullable', 'uuid', Rule::exists('unit_kerja', 'id')],
        ]);

        $roleId = $validated['role_id'] ?? null;
        $roleName = $roleId ? Roles::where('id', $roleId)->value('name') : null;
        if ($roleName === 'unit_kerja' && empty($validated['unit_kerja_id'])) {
            return redirect()->back()->with('error', 'Unit kerja wajib diisi untuk role Unit Kerja.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = $validated['password']; // hashed by cast
        }
        $user->save();

        $user->roles()->sync($roleId ? [$roleId] : []);
        if ($roleName === 'unit_kerja') {
            $user->unit_kerja_id = $validated['unit_kerja_id'];
            $user->save();
        } else {
            $user->unit_kerja_id = null;
            $user->save();
        }

        return redirect()->back()->with('success', 'User updated');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        // detach roles first to keep pivot clean
        $user->roles()->detach();
        $user->delete();

        return redirect()->back()->with('success', 'User deleted');
    }
}
