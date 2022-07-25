<?php

namespace App\Providers;

use App\Models\ClassicLink;
use App\Models\MusicLink;
use App\Models\ShowsLink;
use App\Models\MusicSublink;
use App\Models\ShowsSublink;
use App\Models\Sublink;

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
            'classic' => ClassicLink::class,
            'music' => MusicLink::class,
            'shows' => ShowsLink::class,
            'music' . Sublink::SUFFIX_SUBLINK_TYPE => MusicSublink::class,
            'shows' . Sublink::SUFFIX_SUBLINK_TYPE => ShowsSublink::class,
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
