<?php

namespace App\Providers;

use App\Helpers\BusinessSettingHelper;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

        View::share('appSetting', new BusinessSettingHelper);
    }
}
