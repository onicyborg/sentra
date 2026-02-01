<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Pivots\RoleUser;
use App\Models\Pivots\PermissionRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Roles extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = ['name', 'description'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id')
            ->using(RoleUser::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permissions::class, 'permission_role', 'role_id', 'permission_id')
            ->using(PermissionRole::class)
            ->withPivot('allowed');
    }
}
