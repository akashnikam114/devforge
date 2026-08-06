<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\BusinessSettingHelper;

class AppServiceProvider extends ServiceProvider
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        require_once app_path('Helpers/BusinessSettingHelper.php');
        require_once app_path('Helpers/EncryptionHelper.php');
        require_once app_path('Helpers/GeneralHelper.php');

        View::composer('*', function ($view) {
            $view->with('appSetting', new BusinessSettingHelper);
        });
    }
}
