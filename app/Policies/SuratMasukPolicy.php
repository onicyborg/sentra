<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SuratMasuk;

class SuratMasukPolicy
{
    public function view(User $user): bool
    {
        return $user->hasPermission('surat_masuk.read');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('surat_masuk.create');
    }

    public function verify(User $user): bool
    {
        return $user->hasPermission('surat_masuk.verify');
    }

    public function distribute(User $user): bool
    {
        return $user->hasPermission('surat_masuk.distribute');
    }

    public function followUp(User $user): bool
    {
        return $user->hasPermission('surat_masuk.follow_up');
    }

    public function archive(User $user): bool
    {
        return $user->hasPermission('surat_masuk.archive');
    }
}
