<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifications extends Model
{
    use HasFactory, HasUuid;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
