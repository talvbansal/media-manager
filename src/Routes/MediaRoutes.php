<?php

namespace TalvBansal\MediaManager\Routes;

use Route;

class MediaRoutes
{
    /**
     * Get all of the media manager routes.
     */
    public static function get()
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
