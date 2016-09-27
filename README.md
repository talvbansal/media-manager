<h1 align="center">Media Manager</h1>

<p align="center">
    A basic file manager and uploader for <strong>Laravel</strong> written in <strong>vue.js</strong>
</p>

<div align="center">

    <a href="https://travis-ci.org/talvbansal/media-manager" target="_blank">
        <img src="https://api.travis-ci.org/talvbansal/media-manager.svg" alt="Build Status" />
    </a>
    
    <a href="https://styleci.io/repos/66978705" target="_blank">
        <img src="https://styleci.io/repos/66978705/shield?style=flat" alt="Style CI" />
    </a>
    
    <a href="https://github.com/talvbansal/media-manager/issues" target="_blank">
        <img src="https://img.shields.io/github/issues/talvbansal/media-manager.svg" alt="Issues" />
    </a>
    
    <a href="https://packagist.org/packages/talvbansal/media-manager" target="_blank">
        <img src="https://poser.pugx.org/talvbansal/media-manager/downloads" alt="Downloads" />
    </a>
    
    <a href="https://insight.sensiolabs.com/projects/5079adde-e1f8-437e-bc76-285981053298" target="_blank">
        <img src="https://img.shields.io/sensiolabs/i/5079adde-e1f8-437e-bc76-285981053298.svg?style=flat-square" alt="SensioLabsInsight"/>
    </a>
    
    <a href="https://github.com/talvbansal/media-manager/blob/master/licence" target="_blank">
        <img src="https://poser.pugx.org/talvbansal/media-manager/license" alt="License" />
    </a>
</div>


<h2>Requirements</h2>

- [PHP](https://php.net) >= 5.6
- [Composer](https://getcomposer.org)
- An existing [Laravel 5.3](https://laravel.com/docs/master/installation) project


<h2>Installation</h2>

1. You can download the Media Manager using composer 

    ```
    composer require talvbansal/media-manager
    ```

2. Add `\TalvBansal\MediaManager\Providers\MediaManagerServiceProvider,` to your `config/app.php` file to your register the Media Manager.

3. Add `\TalvBansal\MediaManager\Http\Routes::mediaBrowser();` to your appliction's routes file. This will register all of the routes for the Media Manager.

4. Run `php artisan vendor:publish --tag=media-manager --force` to publish the assets for the media browser.
   
    The Media Manager's assets get published to the following path `/resources/assets/talvbansal`. You can then bundle these with your existing scripts in your projects `gulpfile.js`, for example:
    ```
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

5. Run `php artisan storage:link` to link the `storage/app/public` folder to `public/storage`, your uploaded files will be stored here. Read more about this [on the Laravel documentation](https://laravel.com/docs/5.3/filesystem#the-public-disk).

6. Start using the Media Manager components!

<h2>Getting Started</h2>

The Media Manager is written in `vue.js` and comes bundled with all the dependencies required to get going very quickly.
After you've added the dependencies to your layout if your project doesn't already use `vue.js` you'll need to create a **Vue instance** on the page that you want to use the Media Manager on:

```
<script>
    new Vue({
        el : 'body'
    });
</script>
```

This tells Vue to use the `body` element of your page as its container. Of course you can change this to target a specific element but using `body` means that we can put our custom components anywhere within the `body` tags on a page.

You will also need to add the following to your layout if it doesn't already exist. 
It provides the `csrfToken` used for the `vue-resource` http requests that the Media Manager will make. 
```
<script>
    window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
    ]); ?>
</script>
```

<h2>Media Manager Components</h2>

The Media Manager package will register 2 new usable `vue.js` components:
- `<media-manager>`
- `<media-modal>`
    
The `<media-manager>` component is the core component that provides all of the Media Manager functionality and `<media-modal>` is a component used to build the internal modal windows of the Media Manager. 
The `<media-modal>` component can also be used to open the Media Manager itself inside a modal window.

<h4>Stand Alone Media Manager</h4>

If you just need an instance of the Media Manager getting started is easy. 
Just create a `<media-manager>` tag within the scope of your Vue instance:
```
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
    
<h4>Modal Window Media Manager</h4>

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
```
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

<h2>Notification Events</h2>

So that you can make use of your existing notification system the Media Manager dispatched events that you can listen to using Vue's `events` listeners. The event dispatched for notifications is called `media-manager-notification`. 
When a `media-manager-notification` is dispatched it sends the following information `(message, type, time)`. 

```
<script>
    new Vue({
        el: body,
        data:{
            //...
        },
        events:{ 
            'media-manager-notification': function (message, type, time) {            
                // Your custom notifiction call here...
                console.log(message);
            }
        }
    });
</script>
```

<h2>Selected Item Events</h2>

When opening the Media Manager up via a modal window a new `select` event type can be triggered. 
Like notifications `select` will mean different things depending on the use of the application, there may even be a number of different uses cases for the Media Manager within an application.

To handle instances where different things may need to happen when a `select` event i triggered the Media Manager lets you define a custom `event` name to be dispatched using the `selected-event-name` property:
```
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
```
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
