<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TindakLanjut extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'tindak_lanjut';
    public $timestamps = false;

    protected $fillable = [
        'surat_masuk_id',
        'unit',
        'deskripsi',
    ];

    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class);
    }
}
