<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SuratKeluar;

class SuratKeluarPolicy
{
    public function view(User $user): bool
    {
        return $user->hasPermission('surat_keluar.read');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('surat_keluar.create');
    }

    public function approve(User $user): bool
    {
        return $user->hasPermission('surat_keluar.approve');
    }

    public function send(User $user): bool
    {
        return $user->hasPermission('surat_keluar.send');
    }

    public function archive(User $user): bool
    {
        return $user->hasPermission('surat_keluar.archive');
    }
}
