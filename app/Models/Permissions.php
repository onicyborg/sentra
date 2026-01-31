<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permissions extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = ['permission_key', 'description'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Roles::class, 'permission_role')
            ->withPivot('allowed');
    }
}
