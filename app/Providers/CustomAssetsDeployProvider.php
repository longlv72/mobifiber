<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CustomAssetsDeployProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view)
        {
            // $is_live = env('IS_LIVE');
            $is_live = config('app.app_is_live');
            // dd($is_live);
            $local = '';
            $live = 'public';
            $custom_asset = $is_live ? $live : $local;
            $version_now = strtotime(date('Y-m-d H:i:s'));
            $view->with([
                'custom_asset'=> $custom_asset,
                'version'   => $version_now
            ]);
        });
    }
}
