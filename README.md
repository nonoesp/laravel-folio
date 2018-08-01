# Folio

[![Donate](https://img.shields.io/badge/donate-paypal-blue.svg)](https://www.paypal.me/nonoesp)

Create webs to publish your content with custom templates.

![Folio for Laravel logo.](assets/folio@2x.gif?reload)

## Live Examples

- [nono.ma](http://nono.ma)
- [gettingsimple.com](http://gettingsimple.com)
- [lourdes.ac](http://lourdes.ac)
- [ar-ma.net](http://ar-ma.net/blog)
- [gettingarchitecturedone.com](http://gettingarchitecturedone.com/writing)
- [robotexmachina.com](http://robotexmachina.com)

## Installation

Install using composer:

```bash
composer require nonoesp/folio:dev-master
```

Most packages will be auto-discovered by Laravel.

## Laravel

Next, add (some) providers that are still not auto-discoverable to the `providers` array of `config/app.php`:

```php
    'providers' => [
        // nonoesp/folio dependencies
        Conner\Tagging\Providers\TaggingServiceProvider::class,
        Thujohn\Twitter\TwitterServiceProvider::class,
    ],
```

Then, add the class aliases to the `aliases` array of `config/app.php`:

```php
    'aliases' => [
        // nonoesp/folio dependencies
        'Input' => Illuminate\Support\Facades\Input::class,
        'Markdown' => GrahamCampbell\Markdown\Facades\Markdown::class,
        'Twitter'   => Thujohn\Twitter\Facades\Twitter::class,
        'User' => 'App\User',		
    ],
```

## Middleware

Run the following to installed a set of middlewares required by **Folio**.

```bash
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=middleware
php artisan vendor:publish --provider="Nonoesp\Authenticate\AuthenticateServiceProvider" --tag=middleware
```

Then add the following to `app/Html/Kernel.php`:

```php
protected $middleware = [
        /// nonoesp/folio
        \App\Http\Middleware\SetLocales::class,
        /// nonoesp/authenticate
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,			
        \App\Http\Middleware\RememberLogin::class,        
        /// ...
];

protected $routeMiddleware = [
        /// nonoesp/authenticate
        'login' => \App\Http\Middleware\RequireLogin::class,
        /// ...
];
```

### Sign in with Twitter

You need to publish the config file of `thujon/twitter` and add your Twitter credentials to `config/ttwitter.php`. (You can create a Twitter app at <https://apps.twitter.com/>.)

```bash
php artisan vendor:publish --provider="Thujohn\Twitter\TwitterServiceProvider"
```

## Migrations

First, make sure you remove Laravel's default migration files from `database/migrations` as they can collide
with Folio's migrations.

Let's create the tables required by **Folio** in your database.

First, make sure your database connection is setup properly in you `.env` file.

Publish `rtconner/tagging` migrations:

```bash
php artisan vendor:publish --provider="Conner\Tagging\Providers\TaggingServiceProvider"
```

```bash
php artisan migrate --path=vendor/mpociot/versionable/src/migrations
```

Then, run the migrations:

```bash
php artisan migrate
```

(You can always remove the tables by resetting: `php artisan migrate:reset`.
	But be *careful* as it will remove the contents of your tables.)

## Config

Publish configuration file to `config/folio.php`.

```bash
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=config
```

<!--

## Publish Assets

**Folio** ships with compiled CSS and JS assets, in case you want to use it as is,
they can be published as follows:

```php
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=assets
```

-->

## Publish Development Assets

If you want to customize and compile your own stylesheets,
**Folio** also contains SCSS files with numerous variables
you can tweak and development JavaScript files.
You just need to install a dependencies with `npm`,
publish the development assets,
and generate CSS and JavaScript with Laravel Mix.

Publish the development assets with:

If you haven't done so, publish `nonoesp/folio` development assets.

```bash
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=dev-assets
```

### Install Dependencies (with npm)

First, let's install all our asset dependencies (some of them are SCSS dependencies and others are JavaScript).

```bash
npm install nonoesp/core-scss bourbon font-awesome vue vue-resource vue-focus lodash jquery validate-js
```

### Compile Assets (with Laravel Mix)

Run `npm install` to make sure Laravel Mix is setup properly.

Your `webpack.mix.js` file should look like this.
(You can omit the `.js` or the `.sass` part, just keep whatever you are compiling.)

```javascript
let mix = require('laravel-mix');

// ...

mix.sass('resources/assets/sass/folio.scss', 'public/nonoesp/folio/css');
mix.js('resources/assets/js/folio.js', 'public/nonoesp/folio/js')
   .extract(['vue', 'vue-resource', 'vue-focus', 'jquery', 'validate-js', 'lodash', 'axios']);

```

## Customize Translations

```bash
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=lang
```

## Customize Views

You can publish **Folio** views and customize them. (If you customize a few views and want to get updates from future version for others, you will need to remove the ones you haven't modify and re-publish the views of the package. This won't override your customized view, just the one you delete from your `resources/views/nonoesp/`.)

```bash
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=views
```

## Subscribers Notificaitons

TODO: Explain you need to set a mail driver (amazon ses) and config `config/mail.php` and `config/services.php` and `config/folio.php`.

## Helper Functions

### Show a Folio URL outside Folio

This sample route displays an item with an explicit `slug` without need to have a matching route or domain.

```php
Route::get('/', function ($domain, Request $request) {
    return \Nonoesp\Folio\Controllers\FolioController::showItem($domain, $request, 'sketches');
});
```

## License

Folio is licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Me

I'm [Nono Martínez Alonso](http://nono.ma) (nono.ma), a computational designer with a penchant for design, code, and simplicity. I tweet at [@nonoesp](http://www.twitter.com/nonoesp) and write at [Getting Simple](http://gettingsimple.com/). If you use this package, I would love to hear about it. Thanks!
