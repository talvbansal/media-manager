<?php

namespace TalvBansal\MediaManager\Routes;

use Route;

class MediaRoutes
{
    /**
     * Returns the routes for the media manager,
     * wraps the routes with a route prefix or configured middleware where defined...
     *
     * @return void
     */
    public static function get()
    {
        $middleware = config('media-manager.routes.middleware');
        if (!empty($middleware)) {
            Route::group(['middleware' => $middleware], function () {
                self::loadRoutes();
            });

            return;
        }

        self::loadRoutes();
    }

    /**
     * Get all of the media manager routes.
     */
    private static function loadRoutes()
    {
        // Media Manager Routes
        Route::group(['prefix' => config('media-manager.routes.prefix')], function () {
            Route::get('browser/index', '\TalvBansal\MediaManager\Http\Controllers\MediaController@ls');

            Route::post('browser/file', '\TalvBansal\MediaManager\Http\Controllers\MediaController@uploadFiles');
            Route::delete('browser/file', '\TalvBansal\MediaManager\Http\Controllers\MediaController@deleteFile');
            Route::post('browser/folder', '\TalvBansal\MediaManager\Http\Controllers\MediaController@createFolder');
            Route::delete('browser/folder', '\TalvBansal\MediaManager\Http\Controllers\MediaController@deleteFolder');

            Route::post('browser/rename', '\TalvBansal\MediaManager\Http\Controllers\MediaController@rename');
            Route::get('browser/directories', '\TalvBansal\MediaManager\Http\Controllers\MediaController@allDirectories');
            Route::post('browser/move', '\TalvBansal\MediaManager\Http\Controllers\MediaController@move');
        });
    }
}
