<?php

namespace App\Models\Pivots;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PermissionRole extends Pivot
{
    use HasUuid;

    protected $table = 'permission_role';

    public $incrementing = false;

    protected $keyType = 'string';
}
