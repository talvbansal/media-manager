<?php
/**
 * Created by PhpStorm.
 * User: Fabian
 * Date: 29.05.17
 * Time: 16:50.
 */

return [

    /*
     * Change the bread crumb behaviour.
     * Here you can configure the text output for the root folder of the media-manager...
     */
    'breadcrumb' => [
        'root' => 'Files',
    ],

    /*
     * Change the route behaviour.
     * Here you can apply additional middleware to be applied to media-manager routes
     * as well as setting a custom path prefix for the media-manager routes to be
     * accessible from...
     */
    'routes' => [
        'middleware' => [],
        'prefix'     => env('MEDIA_MANAGER_ROUTE_PREFIX', '/admin/'),
    ],

    /*
     * Set the disk for the media manager to use, this can be one of the disks defined
     * within your projects `config/filesystems.php` file...
     */
    'disk'   => env('MEDIA_MANAGER_STORAGE_DISK', 'public'),

    /*
     * Configure the Access Mode of the uploaded files.
     * By default S3 uploads are private, we're setting them to public here.
     */
    'access' => env('MEDIA_MANAGER_ACCESS', 'public'),
];
