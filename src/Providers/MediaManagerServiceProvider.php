<?php
/**
 * Created by PhpStorm.
 * User: talv
 * Date: 10/07/16
 * Time: 16:04.
 */
namespace TalvBansal\MediaManager\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class MediaBrowserServiceProvider.
 */
class MediaManagerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Load the view files
        $this->loadViewsFrom(MEDIA_MANAGER_BASE_PATH.'/resources/views', 'easel');
        // Load language files
        $this->loadTranslationsFrom(MEDIA_MANAGER_BASE_PATH.'/resources/lang', 'easel');

        if ($this->app->runningInConsole()) {
            $this->defineRoutes();
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Define package base path
        if (!defined('MEDIA_MANAGER_BASE_PATH')) {
            define('MEDIA_MANAGER_BASE_PATH', realpath(__DIR__.'/../../'));
        }
    }

    /**
     * Load Easel specific routes.
     */
    private function defineRoutes()
    {
        /*if (!$this->app->routesAreCached()) {
            \Route::group(['namespace' => 'Easel\Http\Controllers'],
                function ($router) {
                    require MEDIA_MANAGER_BASE_PATH.'/src/Http/routes.php';
                }
            );
        }*/
    }

    /**
     * Publish assets / images / css / js / views to host application
     * This is only when the application is run in the console.
     */
    private function defineResources()
    {
        $this->publishes([
            MEDIA_MANAGER_BASE_PATH.'/public' => base_path('public'),
        ]);

        $this->publishes([
            MEDIA_MANAGER_BASE_PATH.'/resources/assets/storage' => storage_path('app/public'),
        ]);
    }
}
