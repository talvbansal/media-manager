<?php

namespace TalvBansal\MediaManager\Http;

use Route;

class Routes
{
    public static function mediaBrowser()
    {

        // Media Manager Routes
        Route::get('/admin/media', '\TalvBansal\MediaManager\Http\Controllers\MediaController@index');
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
