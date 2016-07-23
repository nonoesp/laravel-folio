# Laravel Writing Package

A blogging system for Laravel 5.

## Installation

Run `composer require nonoesp/writing:*` on the Terminal (or add the dependency manually to your `composer.json` as `"nonoesp/writing": "*"`, then run `composer update` on the Terminal).

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

`php artisan vendor:publish --provider=“Nonoesp\Writing\WritingServiceProvider” --tag=config`

On the config/writing.php file you can customize:

* `use_path_prefix` — A boolean to toggle between using a path prefix for all the routes created by the package or deactivating it. Just set it to `true` or `false`.
* `path` — A string that defines your prefix path (only used if use_path_prefix is set to `true`).
* `protected_uris` — An array to avoid the Writing package to override other existing routes (specially when you set use_path_prefix to false).

## Dependencies

* `nonoesp/thinker` package.
* `rtconner/laravel-tagging` package.
* `Article` model, which needs to be added to your config/app.php file (as mentioned before).
* `layout.main` view with a @section(‘content’), which this package will use.

## Stylesheets

The package provides HTML structure but, for the moment being, it is CSS agnostic.

* c-article
* c-load-more
* o-wrap
* o-video-thumb

## To-dos

* Add explicit slugs.
* Add migrations to create database tables.
* Test Disqus implementation for comments.
* Put user route inside controller.
* Embed Article model inside package — there is a issue with rtconner/tagging not working when the model is owned by the package.

## Features

* **Article recipients**. Make articles only visible to certain Twitter handles.

## Release Notes

### v0.8.0

* Added article recipients. Add a list of Twitter handles to your article to restrict their visibility to those users when they are logged in. Otherwise, the site just shows visitor posts with no recipients.

### v0.7.2

* Introduced special tag CSS class names. A class like c-article-tagName will be added to a series of special tags or categories when a post contains them. This allows to create custom CSS for posts tagged with an specific tag.

### v0.7.1

* Selective routes only if slug exists.

## License

Writing is licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Me

I tweet at [@nonoesp](http://www.twitter.com/nonoesp) and blog at [nono.ma/says](http://nono.ma/says). If you use this package, I would love to hear about it. Thanks!