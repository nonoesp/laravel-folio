# Laravel Writing Package

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

* ```nonoesp/thinker``` needs to be installed.

* You need to provide an Article model and add it to your config/app.php file (as mentioned before).

* You need to provide a `layout.main` view with a @section(‘content’), which this package will use.

## Stylesheets

The package provides HTML structure but, for the moment being, it is CSS agnostic.

* c-article
* c-load-more
* o-wrap
* o-video-thumb

## TODO

* Put user route inside controller.
* Embed Article model inside package — there is a issue with rtconner/tagging not working when the model is owned by the package.

## Release Notes

### v0.8.0

* Introduced special tag CSS class names. A class like c-article-tagName will be added to a series of special tags or categories when a post contains them. This allows to create custom CSS for posts tagged with an specific tag.

### v0.7.1

* Selective routes only if slug exists.

## License

Writing is licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Me

I tweet at [@nonoesp](http://www.twitter.com/nonoesp) and blog at [nono.ma/says](http://nono.ma/says). If you use this package, I would love to hear about it. Thanks!