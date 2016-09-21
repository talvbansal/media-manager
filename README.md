<h1 align="center">Media Manager</h1>

<p align="center">A basic filemanager and uploader for Laravel</p>

<div align="center">

    <a href="https://travis-ci.org/talvbansal/media-manager" target="_blank">
        <img src="https://api.travis-ci.org/talvbansal/media-manager.svg" alt="Build Status" />
    </a>
    
    <a href="https://styleci.io/repos/63001540" target="_blank">
        <img src="https://styleci.io/repos/63001540/shield?style=flat" alt="Style CI" />
    </a>
    
    <a href="https://github.com/talvbansal/media-manager/issues" target="_blank">
        <img src="https://img.shields.io/github/issues/talvbansal/media-manager.svg" alt="Issues" />
    </a>
    
    <a href="https://packagist.org/packages/talvbansal/media-manager" target="_blank">
        <img src="https://poser.pugx.org/talvbansal/media-manager/downloads" alt="Downloads" />
    </a>
    
    <a href="https://insight.sensiolabs.com/projects/06d23269-ac1d-4465-b542-9c38b31f8d91" target="_blank">
        <img src="https://img.shields.io/sensiolabs/i/06d23269-ac1d-4465-b542-9c38b31f8d91.svg?style=flat-square" alt="SensioLabsInsight"/>
    </a>
    
    <a href="https://github.com/talvbansal/media-manager/blob/master/licence" target="_blank">
        <img src="https://poser.pugx.org/talvbansal/media-manager/license" alt="License" />
    </a>
</div>


<h3>Requirements</h3>

- [PHP](https://php.net) >= 5.6
- [Composer](https://getcomposer.org)
- An existing [Laravel 5.3](https://laravel.com/docs/master/installation) project


<h3>Installation</h3>

1. You can download the Media Manager using composer 

    ```
    composer require talvbansal/media-manager
    ```

2. To register the media manager, you will need to add the Media Manager service provider to your `config/app.php` file

    ```
    \TalvBansal\MediaManager\Providers\MediaManagerServiceProvider,
    ```
3. The service provider **does not** automatically register the routes needed for the media manager to work since it is likely that you will want to add middleware to the routes. You can add the following line to your routes file and then wrap it with whichever middleware is necessary for your application

    ```
    \TalvBansal\MediaManager\Http\Routes::mediaBrowser();
    ```

4. Publish the assets for the media browser
    
    ```
    php artisan vendor:publish --tag=media-manager
    ```
   
