<?php

namespace App\Providers;

use App\Models\Archive;
use App\Policies\ArchivePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    // Daftarkan semua policy di sini
    protected $policies = [
        Archive::class => ArchivePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}