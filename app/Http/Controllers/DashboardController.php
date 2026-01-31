<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Arsip;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\Permissions;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $usersCount = User::query()->count();
        $rolesCount = Roles::query()->count();
        $permissionsCount = Permissions::query()->count();

        $suratMasukCount = class_exists(SuratMasuk::class) ? SuratMasuk::query()->count() : 0;
        $suratKeluarCount = class_exists(SuratKeluar::class) ? SuratKeluar::query()->count() : 0;
        $arsipCount = class_exists(Arsip::class) ? Arsip::query()->count() : 0;

        $activities = ActivityLog::query()
            ->latest('created_at')
            ->limit(10)
            ->get(['id','user_id','method','url','created_at']);

        return view('welcome', [
            'usersCount' => $usersCount,
            'rolesCount' => $rolesCount,
            'permissionsCount' => $permissionsCount,
            'suratMasukCount' => $suratMasukCount,
            'suratKeluarCount' => $suratKeluarCount,
            'arsipCount' => $arsipCount,
            'activities' => $activities,
        ]);
    }
}
