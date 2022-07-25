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
            'classic' => 'App\Models\ClassicLink',
            'music' => 'App\Models\MusicLink',
            'shows' => 'App\Models\ShowsLink',
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
