<?php

namespace App\Models\Pivots;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleUser extends Pivot
{
    use HasUuid;

    protected $table = 'role_user';

    public $incrementing = false;

    protected $keyType = 'string';
}
