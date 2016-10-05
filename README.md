# Media Manager

>A basic file manager and uploader for **Laravel** written in **vue.js**

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
- An existing [Laravel 5.3](https://laravel.com/docs/master/installation) project

## # Installation

To get started, install Media Manager via the Composer package manager: 
```bash
composer require talvbansal/media-manager
```

Next, register the Media Manager service provider in the `providers` array of your `config/app.php` configuration file:
```php
\TalvBansal\MediaManager\Providers\MediaManagerServiceProvider::class,
```

The Media Manager service provider **does not** automatically register routes for the Media Manager to work. This is so that you can add custom middleware around those routes. You can register all of the routes required for the Media Manager by adding the following to your `routes/web.php` file: 
```php
\TalvBansal\MediaManager\Routes\MediaRoutes::get();
```

After registering the Media Manager service provider, you should publish the Media Manager assets using the `vendor:publish` Artisan command: 
```bash
php artisan vendor:publish --tag=media-manager --force
```
Media Manager assets are **not** published to the `public` folder as would be normally expected, instead they will be published to `/resources/assets/talvbansal`.
You can then bundle these with your existing scripts in your projects `gulpfile.js`, for example:
```javascript
//gulpfile.js
var elixir = require('laravel-elixir');

require('laravel-elixir-vue');

elixir(function(mix) {

    // Add additional styles...
    mix.sass([
        '../talvbansal/media-manager/css/media-manager.css',
        'app.scss'
    ]);

    // Add dependencies and components...
    mix.webpack([
        '../talvbansal/media-manager/js/media-manager.js',
        'app.js'
    ]);

    // Copy SVG images into the public directory...
    mix.copy( 'resources/assets/talvbansal/media-manager/fonts', 'public/fonts' );
});

```

The media manager uses the `public` disk to store its uploads. The storage path for the `public` disk by default is `storage/app/public`. To make these files accessible from the web, use the following `storage:link` artisan command to generate a symbolic link to `public/storage`:
```bash
php artisan storage:link
```
Read more about the public disk [on the Laravel documentation](https://laravel.com/docs/5.3/filesystem#the-public-disk).

## # Getting Started

The Media Manager is written in `vue.js` and comes bundled with all the dependencies required to get going very quickly.
After you've added the dependencies to your layout if your project doesn't already use `vue.js` you'll need to create a **Vue instance** on the page that you want to use the Media Manager on:

```javascript
<script>
    new Vue({
        el : 'body'
    });
</script>
```

This tells Vue to use the `body` element of your page as its container. Of course you can change this to target a specific element but using `body` means that we can put our custom components anywhere within the `body` tags on a page.

You will also need to add the following to your layout if it doesn't already exist.
It provides the `csrfToken` used for the `vue-resource` http requests that the Media Manager will make.
```javascript
<script>
    window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
    ]); ?>
</script>
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
    <media-manager></media-manager>
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
3. We'll need a way to open and close the modal window.
    - Create a boolean property on your root vue instance to hold the visible state of the modal window.
    - Add the property to the Media Manager's and Modal Window's `show` property.
    - Make sure they're both set to `show.sync` so that changes to its value can be made allowing the window can close itself
    - Create a button to open the modal window and get it change the property bound to the modal window's `show` property to `true`

Here is an example of all of the above:
```html
<body>
    <media-modal :show.sync="showMediaManager">
        <media-manager
                :show.sync="showMediaManager"
                :is-modal="true"
        >
        </media-manager>
    </media-modal>

    <button @click="showMediaManager = true">
        Show Media Manager
    </button>

    <script>
        new Vue({
        el: body,
            data: {
                showMediaManager: false,
            }
        });
    </script>
</body>
```

As well as providing all of the functionality that the normal `<media-manager>` component gives, when in a modal window buttons to close the window and `select` files are rendered.

## # Notification Events

So that you can make use of your existing notification system the Media Manager dispatched events that you can listen to using Vue's `events` listeners. The event dispatched for notifications is called `media-manager-notification`.
When a `media-manager-notification` is dispatched it sends the following information `(message, type, time)`.

```html
<script>
    new Vue({
        el: body,
        data:{
            //...
        },
        events:{
            'media-manager-notification': function (message, type, time) {
                // Your custom notification call here...
                console.log(message);
            }
        }
    });
</script>
```

## # Selected Item Events

When opening the Media Manager up via a modal window a new `select` event type can be triggered.
Like notifications `select` will mean different things depending on the use of the application, there may even be a number of different uses cases for the Media Manager within an application.

To handle instances where different things may need to happen when a `select` event i triggered the Media Manager lets you define a custom `event` name to be dispatched using the `selected-event-name` property:
```html
<media-modal :show.sync="showMediaManager">
    <media-manager
            :is-modal="true"
            :selected-event-name.sync="selectedEventName"
            :show.sync="showMediaManager"
    >
    </media-manager>
</media-modal>
 ```

When `select` is called a custom event is dispatched that you can listen to using Vue's `events` listeners.
The name event dispatched is dynamically generated by the `selected-event-name` property's value prefixed with `media-manager-selected-`
For example if the `selected-event-name` property was set to `editor` the event dispatched would be `media-manager-selected-editor` and we could handle the event as follows:
```javascript
<script>
    new Vue({
        el : 'body',
        data:{
            showMediaManager: false,
            selectedEventName: 'editor'
        },

        events: {
            'media-manager-selected-editor': function (file) {
                // Do something with the file info...
                console.log(file.name);
                console.log(file.mimeType);
                console.log(file.relativePath);
                console.log(file.webPath);

                // Hide the Media Manager...
                this.showMediaManager = false;
            }
        }
    })
</script>
```
The prefix on the event names is to avoid / reduce any potential event names clashes.
