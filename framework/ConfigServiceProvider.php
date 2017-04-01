<?php

namespace Phine;

use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('config', function ($app) {
            return [
                'view.paths' => [
                        realpath(base_path('resources/views')),
                ],
                'view.compiled' => realpath(storage_path()),
            ];
        });
    }
}
