<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Policies\SuratKeluarPolicy;
use App\Policies\SuratMasukPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        SuratMasuk::class => SuratMasukPolicy::class,
        SuratKeluar::class => SuratKeluarPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
