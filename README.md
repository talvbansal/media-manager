# Media Manager

>Media manager is a basic file uploader and manager component for **Laravel** written in **Vue.js 2.0**

_For **Vue.js 1.x** please use [Version 1.0.x](https://github.com/talvbansal/media-manager/tree/v1.0.6)_

[![Build Status](https://api.travis-ci.org/talvbansal/media-manager.svg)](https://travis-ci.org/talvbansal/media-manager)
[![Style CI](https://styleci.io/repos/66978705/shield?style=flat)](https://styleci.io/repos/66978705)
[![Issues](https://img.shields.io/github/issues/talvbansal/media-manager.svg)](https://github.com/talvbansal/media-manager/issues)
[![Total Downloads](https://poser.pugx.org/talvbansal/media-manager/downloads)](https://packagist.org/packages/talvbansal/media-manager)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/5079adde-e1f8-437e-bc76-285981053298.svg?style=flat)](https://insight.sensiolabs.com/projects/5079adde-e1f8-437e-bc76-285981053298)
[![License](https://poser.pugx.org/talvbansal/media-manager/license)](https://github.com/talvbansal/media-manager/blob/master/licence)

## # Introduction
Media Manager provides a simple way for users to upload and manage content to be used throughout your project.

## # Requirements

- [PHP](https://php.net) >= 5.6
- [Composer](https://getcomposer.org)
- An existing [Laravel 5.3+](https://laravel.com/docs/master/installation) project

## # Installation

To get started, install Media Manager via the Composer package manager: 
```bash
composer require talvbansal/media-manager
```

Next, register the Media Manager service provider in the `providers` array of your `config/app.php` configuration file (For Laravel 5.5 users, you do not need to register as it is now auto discovered):
```php
\TalvBansal\MediaManager\Providers\MediaManagerServiceProvider::class,
```

## # Routing and Middleware
The Media Manager service provider **does not** automatically register routes for the Media Manager to work. This is so that you can add custom middleware around those routes. You can register all of the routes required for the Media Manager by adding the following to your `routes/web.php` file: 
```php
\TalvBansal\MediaManager\Routes\MediaRoutes::get();
```

Should you wish to add middleware around the Media Manager routes you can using the normal `Route::group` syntax or using the Media Managers config file. 
```
php artisan vendor:publish --tag=media-manager
```
After publishing the packages assets a new config file will appear at `/config/media-manager.php`. 
Simply add the desired middleware to to the middleware array.

If you want to change the root prefix of the routes from `/admin/` you can do so by changing the `media-manager.routes.prefix` value in the config file published above. 
However amending the prefix value will require you to pass a new `prefix` prop to your media-manager markup that corresponds directly to this value.

## # Assets
After registering the Media Manager service provider, you should publish the Media Manager assets using the `vendor:publish` Artisan command: 
```bash
php artisan vendor:publish --tag=media-manager --force
```
Media Manager assets are **not** published to the `public` folder as would be normally expected, instead they will be published to `/resources/assets/talvbansal`.
Since the Media Manger is written in `vue.js 2.0` you'll need to use webpack or another bundler to get the code ready for the browser. You can then bundle these with your existing project assets.

##### Examples 

First you'll need to add the media-manager reference within your `resources/assets/js/app.js` file:

```javascript
require('./bootstrap');

// Add this line...
require('./../talvbansal/media-manager/js/media-manager');

const app = new Vue({
    el: '#app'
});
```

##### # Laravel Mix (Laravel 5.4+)
```javascript

// -- webpack.mix.js --
const { mix } = require('laravel-mix');

// Copy SVG images into the public directory...
mix.copy('resources/assets/talvbansal/media-manager/fonts/', 'public/fonts/');
```


Then make sure that the styles are bundled and icons copied to the public directory:

```sass
// -- app.scss --
@import "../talvbansal/media-manager/css/media-manager.css";
```

##### # Laravel Elixir (Laravel 5.3)
```javascript
// -- gulpfile.js --
var elixir = require('laravel-elixir');
require('laravel-elixir-vue-2');

elixir(function(mix) {
    // Copy SVG images into the public directory...
    mix.copy( 'resources/assets/talvbansal/media-manager/fonts', 'public/fonts' );
    
    // Add the media-manager styles to the app.css file
    mix.styles(
        [
            "../talvbansal/media-manager/css/media-manager.css",
            "app.scss"
        ],'public/css/app.css'
    );
    
});

```

By default the media manager uses the `public` disk to store its uploads. The storage path for the `public` disk by default is `storage/app/public`. To make these files accessible from the web, use the following `storage:link` artisan command to generate a symbolic link to `public/storage`:
```bash
php artisan storage:link
```
Read more about the public disk [on the Laravel documentation](https://laravel.com/docs/master/filesystem#the-public-disk).

If you wish to change the disk that media manager stores its files to you can create a new entry in your projects `.env` file with the name of `MEDIA_MANAGER_STORAGE_DISK` and the name of the disk configured within `config/filesystems.php`.
Any [flysystem](https://flysystem.thephpleague.com/) adapter which supports the `url` method should work.

## Note:
Some cloud flysystems like `AWS S3` supports access modes. All File Uploads via media-manger are `public` by default. It can be changed by specifying it in env `MEDIA_MANAGER_ACCESS` or directly via config `media-manager.php` if you have published the package's config in your project.

## # Getting Started

The Media Manager is written in `vue.js 2.0` and comes bundled with all the dependencies required to get going very quickly.

To avoid CSRF issues you will need to add the following to your layout if it does not already exist:
```html
    <meta name="csrf-token" content="{{ csrf_token() }}">
```

## # Media Manager Components

The Media Manager package will register 2 new usable `vue.js` components:
- `<media-manager>`
- `<media-modal>`

The `<media-manager>` component is the core component that provides all of the Media Manager functionality and `<media-modal>` is a component used to build the internal modal windows of the Media Manager.
The `<media-modal>` component can also be used to open the Media Manager itself inside a modal window.

#### # Stand Alone Media Manager

If you just need an instance of the Media Manager getting started is easy.
Just create a `<media-manager>` tag within the scope of your Vue instance:
```html
<body>
    <div id="app">
        <media-manager></media-manager>
    </div>
</body>
```
This will create a Media Manager that will allow you to do all of the following:
- Navigate directories
- Upload new files
- Create new folders
- Rename items
- Move items
- Delete items
    
#### Modal Window Media Manager

Setting up a Media Manager within a modal window requires a bit more markup and configuration.

You'll need to do the following:

1. Create a `<media-manager>` component nested within a `<media-modal>`  component.
2. Add the `:is-modal="true"` property to the Media Manager component : `<media-manager :is-modal="true">`
3. Create a way to open and close the modal window.
    - Within the data object of your root Vue instance create a boolean property to hold the visible state of the modal window with a default value of `false`, `showMediaManager = false`.
    - Add a `v-if` directive to the `<media-modal>` component and use the newly created `showMediaManager` property to toggle the modal window's visibility, `<media-modal v-if="showMediaManager"></media-modal>`.
    - Create a button to open the modal window and get it change the property bound to the modal window's `show` property to `true`
    - Add listeners for the `@media-modal-close` event to the `<media-modal>` and `<media-manager>` components so that they can close the modal window

Here is an example of all of the above:
```html
<body>
    <div id="app">
        <media-modal v-if="showMediaManager" @media-modal-close="showMediaManager = false">
            <media-manager
                :is-modal="true"
                @media-modal-close="showMediaManager = false"
            >
            </media-manager>
        </media-modal>
    
        <button @click="showMediaManager = true">
            Show Media Manager
        </button>
    </div>

    <script>
        new Vue({
        el: '#app',
            data: {
                showMediaManager: false,
            }
        });
    </script>
</body>
```

As well as providing all of the functionality that the normal `<media-manager>` component gives, when displayed within a modal window, buttons to close the window and `select` files are rendered.

## # Notification Events

So that you can make use of your projects existing notification system the Media Manager emits events than can be listened on using a separate `Vue` instance that is automatically created and added to the `window` with a name of `eventHub` (if `window.eventHub` doesn't already exist). 
The event emitted for notifications is called `media-manager-notification` and has the following signature : `(message, type, time)`. 

    - message: string
    - type : string
    - time : int

A listener can be added to either the `created()` method of your root `vue` instance or a component:

```html
<script>
    new Vue({
        el: '#app',
        data:{
            //...
        },
        created: function(){
            window.eventHub.$on('media-manager-notification', function (message, type, time) {
                // Your custom notifiction call here...
                console.log(message);
            });
        }
    });
</script>
```
## # Selected Item Events

When selecting an item through a Media Manager instance that has been opened within a modal window a new `select` event type is emitted.
Like notifications `select` will mean different things depending on the use of the application, there may even be a number of different uses cases for the Media Manager within an application.

To handle instances where different things may need to happen when a `select` event is triggered the Media Manager lets you define a custom `event` name to be emitted using the `selected-event-name` property:
```html
<media-modal v-if="showMediaManager" @media-modal-close="showMediaManager = false">
    <media-manager
        :is-modal="true"
        :selected-event-name="selectedEventName"
        @media-modal-close="showMediaManager = false"
    >
    </media-manager>
</media-modal>
 ```

When `select` is called a custom event is dispatched that can be listened for using Vue's `events` listeners.
The event name dispatched is dynamically generated by the `selected-event-name` property's value prefixed with `media-manager-selected-`
For example if the `selected-event-name` property was set to `editor` the event dispatched would be `media-manager-selected-editor` and we could handle the event using the `window.eventHub` as follows:
```javascript
<script>
    new Vue({
        el : 'body',
        data:{
            showMediaManager: false,
            selectedEventName: 'editor'
        },

        mounted(){
            window.eventHub.$on('media-manager-selected-editor', (file) => {
                // Do something with the file info...
                console.log(file.name);
                console.log(file.mimeType);
                console.log(file.relativePath);
                console.log(file.webPath);

                // Hide the Media Manager...
                this.showMediaManager = false;
            });
        }
    })
</script>
```
The prefix on the event names is to avoid / reduce any potential event names clashes on the event hub.
