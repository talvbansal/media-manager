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

        // Publishes config file
        $this->publishes([
            MEDIA_MANAGER_BASE_PATH.'/src/Config/media-manager.php' => config_path('media-manager.php'),
        ], 'media-manager');

        $this->mergeConfigFrom(
            MEDIA_MANAGER_BASE_PATH.'/src/Config/media-manager.php', 'media-manager'
        );
    }

    /**
     * Publish assets to host application
     * This is only when the application is run in the console.
     */
    private function defineResources()
    {
        $this->publishes([
            MEDIA_MANAGER_BASE_PATH.'/public' => resource_path('/assets/talvbansal/media-manager'),
        ], 'media-manager');
    }
}
