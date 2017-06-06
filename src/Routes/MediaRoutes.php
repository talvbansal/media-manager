<?php

namespace TalvBansal\MediaManager\Routes;

use Route;

class MediaRoutes
{
    /**
     * Checks, if a config-file is and if custom middleware is set in the config file.
     * Gives all the media manager routes back.
     */
    public static function get()
    {
        if (empty(config('media-manager.routes.middleware'))) {

            // If no config is set, routes will be loaded normally without middleware..
            self::loadRoutes();
        } else {

            // .. otherwise the routes will be wrapped in a middleware
            Route::group(['middleware' => config('media-manager.routes.middleware')], function () {
                self::loadRoutes();
            });
        }
    }

    /**
     * Get all of the media manager routes.
     */
    private static function loadRoutes()
    {
        // Media Manager Routes
        Route::get('/admin/browser/index', '\TalvBansal\MediaManager\Http\Controllers\MediaController@ls');

        Route::post('admin/browser/file', '\TalvBansal\MediaManager\Http\Controllers\MediaController@uploadFiles');
        Route::delete('/admin/browser/delete', '\TalvBansal\MediaManager\Http\Controllers\MediaController@deleteFile');
        Route::post('/admin/browser/folder', '\TalvBansal\MediaManager\Http\Controllers\MediaController@createFolder');
        Route::delete('/admin/browser/folder', '\TalvBansal\MediaManager\Http\Controllers\MediaController@deleteFolder');

        Route::post('/admin/browser/rename', '\TalvBansal\MediaManager\Http\Controllers\MediaController@rename');
        Route::get('/admin/browser/directories', '\TalvBansal\MediaManager\Http\Controllers\MediaController@allDirectories');
        Route::post('/admin/browser/move', '\TalvBansal\MediaManager\Http\Controllers\MediaController@move');
    }
}
