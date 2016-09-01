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
        $this->loadViewsFrom(MEDIA_MANAGER_BASE_PATH.'/resources/views', 'media-manager');
        // Load language files
        $this->loadTranslationsFrom(MEDIA_MANAGER_BASE_PATH.'/resources/lang', 'media-manager');

        if ($this->app->runningInConsole()) {
            $this->defineResources();
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
     * Publish views to host application
     * This is only when the application is run in the console.
     */
    private function defineResources()
    {
        $this->publishes([
            MEDIA_MANAGER_BASE_PATH.'/public' => public_path('/vendor/talvbansal/mediamanager'),
        ], 'media-manager');
    }
}
