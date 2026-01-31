<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disposisi extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'disposisi';
    public $timestamps = false;

    protected $fillable = [
        'surat_masuk_id',
        'dari_user',
        'ke_unit',
        'catatan',
        'status',
    ];

    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class);
    }

    public function dari(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dari_user');
    }
}
