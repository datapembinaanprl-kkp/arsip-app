<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => fn() => $request->user() 
                ? [
                    'id'          => $request->user()->id,  
                    'name'        => $request->user()->name,
                    'email'       => $request->user()->email,
                    'avatar_url'  => $request->user()->avatar_url,
                    'roles'       => $request->user()->getRoleNames()->values(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                    'tim_kerja'   => $request->user()->timKerja?->only(['id', 'nama', 'kode']),
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            
            'ziggy' => fn () => [
                ...(new \Tighten\Ziggy\Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ]);
    }
}