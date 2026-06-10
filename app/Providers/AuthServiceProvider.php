<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Di sini Lumen langsung membaca Token dari Header Postman tanpa bantuan file config
        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                $key = explode(' ', $request->header('Authorization'));
                $token = $key[1] ?? '';
                
                // Mencari user di database yang memiliki api_token tersebut
                return \App\Models\User::where('api_token', $token)->first();
            }
        });
    }
}
