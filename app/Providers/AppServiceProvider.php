<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Pulse\Facades\Pulse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewPulse', fn($user) => $user->id === 1);

        Pulse::user(fn($user) => [
            'name' => Str::reverse($user->name),
            'extra' => fake()->sentence(),
            'avatar' => 'https://ui-avatars.com/api/?name='.$user->name.'&background='.Str::of(fake()->hexColor())->substr(1).'&color='.Str::of(fake()->hexColor())->substr(1),
        ]);
    }
}
