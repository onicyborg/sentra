<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arsip extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'arsip';
    public $timestamps = false;

    protected $fillable = [
        'jenis_surat',
        'surat_id',
        'archived_at',
    ];
}
