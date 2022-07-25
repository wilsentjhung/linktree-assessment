<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Relation::enforceMorphMap([
            'classicLink' => 'App\Models\ClassicLink',
            'musicLink' => 'App\Models\MusicLink',
            'showsLink' => 'App\Models\ShowsLink',
            'musicSublink' => 'App\Models\MusicSublink',
            'showsSublink' => 'App\Models\ShowsSublink',
        ]);

        Sanctum::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
