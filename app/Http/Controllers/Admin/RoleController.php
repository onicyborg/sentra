<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('can:role.manage');
    }

    public function index()
    {
        $roles = Roles::with(['permissions' => function ($q) {
            $q->select('permissions.id', 'permission_key', 'description');
        }])->orderBy('name')->get();
        $permissions = Permissions::orderBy('permission_key')->get();

        return view('roles.index', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['in:0,1'],
        ]);

        $role = Roles::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        $this->syncPermissions($role, $validated['permissions'] ?? []);

        return back()->with('success', 'Role created');
    }

    public function show(string $id)
    {
        $role = Roles::with(['permissions' => function ($q) {
            $q->select('permissions.id', 'permission_key', 'description');
        }])->findOrFail($id);

        $perm = [];
        foreach ($role->permissions as $p) {
            $perm[$p->id] = (bool) $p->pivot->allowed;
        }

        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'permissions' => $perm,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $role = Roles::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', Rule::unique('roles', 'name')->ignore($role->id)],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['in:0,1'],
        ]);

        $role->name = $validated['name'];
        $role->description = $validated['description'] ?? null;
        $role->save();

        $this->syncPermissions($role, $validated['permissions'] ?? []);

        return back()->with('success', 'Role updated');
    }

    protected function syncPermissions(Roles $role, array $permissionsForm)
    {
        // Build a map of all permissions with allowed flags (default 0)
        $allPermissions = Permissions::pluck('id');

        $payload = [];
        foreach ($allPermissions as $pid) {
            $allowed = isset($permissionsForm[$pid]) && (int)$permissionsForm[$pid] === 1;
            $payload[$pid] = ['allowed' => $allowed];
        }

        // Use sync with pivot data
        $role->permissions()->sync($payload);
    }

    public function destroy(string $id)
    {
        $role = Roles::withCount('users')->findOrFail($id);

        if ($role->users_count > 0) {
            return back()->with('error', 'Role tidak dapat dihapus karena sudah digunakan oleh user.');
        }

        // detach permissions then delete role
        $role->permissions()->detach();
        $role->delete();

        return back()->with('success', 'Role deleted');
    }
}
