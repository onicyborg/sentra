<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware('can:permission.manage');
    }

    public function index()
    {
        $permissions = Permissions::orderBy('permission_key')->get();
        return view('permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'permission_key' => [
                'required',
                'string',
                'max:190',
                'unique:permissions,permission_key',
                // lowercase letters, digits, dot and underscore only
                'regex:/^[a-z0-9._]+$/',
            ],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $perm = Permissions::create([
            'permission_key' => $validated['permission_key'],
            'description' => $validated['description'] ?? null,
        ]);

        // default allowed = false for all roles
        $roleIds = Roles::pluck('id');
        if ($roleIds->count()) {
            foreach ($roleIds as $rid) {
                // attach with allowed=false without detaching existing links
                $role = Roles::find($rid);
                $role?->permissions()->syncWithoutDetaching([
                    $perm->id => ['allowed' => false],
                ]);
            }
        }

        return back()->with('success', 'Permission created');
    }

    public function show(string $id)
    {
        $perm = Permissions::findOrFail($id);
        return response()->json([
            'id' => $perm->id,
            'permission_key' => $perm->permission_key,
            'description' => $perm->description,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $perm = Permissions::findOrFail($id);

        $validated = $request->validate([
            // permission_key is read-only; do not allow change
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $perm->description = $validated['description'] ?? null;
        $perm->save();

        return back()->with('success', 'Permission updated');
    }

    public function destroy(string $id)
    {
        $perm = Permissions::withCount([
            'roles as roles_allowed_count' => function ($q) {
                $q->where('permission_role.allowed', true);
            }
        ])->findOrFail($id);

        // Safe delete: only if not enabled on any role (allowed=true)
        if ($perm->roles_allowed_count > 0) {
            return back()->with('error', 'Permission tidak dapat dihapus karena sudah diaktifkan pada salah satu role.');
        }

        // Detach any existing pivot rows (allowed=false defaults) before delete
        $perm->roles()->detach();
        $perm->delete();
        return back()->with('success', 'Permission deleted');
    }
}
