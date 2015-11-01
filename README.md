# Writing System for Laravel 5

A blogging system for Laravel 5.

## Installation

Run `compose require nonoesp/writing:*`

Add `'Nonoesp/Writing/WritingServiceProvider',` to `providers` in `/app/config/app.php`

Add `'Writing' => Nonoesp\Writing\Facades\Writing::class,` to your `aliases`.

The package requires an Article and a User model created on the main app. To avoid setup on the package part, you will have to add both aliases in your config/app.php `aliases` array, as follows.

```
'User' => 'App\User',
'Article' => 'App\Article',
```

## Config

There are some settings to customize the way you use the **Writing** package.

Even though you can publish the whole bundle of assets, I recommend to start by only publishing the config file and the required JavaScript assets.

First, run this command, and you will get a customizable config file under `config/writing.php`.

`php artisan asset:publish --provider=“Nonoesp\Writing\WritingServiceProvider” --tag=config`

On the config/writing.php file you can customize:

* `use_path_prefix` — A boolean to toggle between using a path prefix for all the routes created by the package or deactivating it. Just set it to `true` or `false`.
* `path` — A string that defines your prefix path (only used if use_path_prefix is set to `true`).
* `protected_uris` — An array to avoid the Writing package to override other existing routes (specially when you set use_path_prefix to false).

## Dependencies

This package requires ```nonoesp/thinker```.

This package assumes your Laravel app is providing a `layout.main` view which is used as a view template, extended by laravel-writing. Also, the content of this package appears in the @section('content') section.

## SCSS Dependencies

* c-article
* c-load-more
* o-wrap
* o-video-thumb

## TODO

* Put user route inside controller.

***

# Notes++

*This should be passed later on to Notes-Laravel*

To use the controllers inside your own workbench/package, you need to manually add "src/controllers" to your classmap, and run `composer dump-autoload`. Then you are good to go. Make sure your controller extends `\BaseController` and not `BaseController`, and then use your controller as `Route::get('path', 'Vendor\Package\ControllerName@yourMethod');`.


## Publish Package Assets while Developing in the Workbench

The following command will copy your assets into `/public/packages/vendor/package/`. Development should be continued on the workbench. Then you can run the command again if you want to update the previously published assets.

// Deprecated (Laravel 4)

`php artisan asset:publish --bench="vendor/package"`

## License

Writing is licensed under the MIT license. (http://opensource.org/licenses/MIT)

## Me

I tweet at [@nonoesp](http://www.twitter.com/nonoesp) and blog at [nono.ma/says](http://nono.ma/says). If you use this package, I would love to hear about it. Thanks!