# A Simple Blogging Package for Laravel

Hi! This branch supports 5.2.

## Installation

Begin by installing this package through Composer. Edit your project’s `composer.json` file to require `nonoesp/writing`.

```
"require": {
	"nonoesp/writing": "5.2.*"
}
```

Next, update Composer from the Terminal:

```
composer update
```

Next, add the new providers to the `providers` array of `config/app.php`:

```
	'providers' => [
		// ...
    // nonoesp/writing
    Nonoesp\Writing\WritingServiceProvider::class,        
    Nonoesp\Thinker\ThinkerServiceProvider::class,  
    Nonoesp\Authenticate\AuthenticateServiceProvider::class,          
    VTalbot\Markdown\MarkdownServiceProvider::class,
    Conner\Tagging\Providers\TaggingServiceProvider::class,
    Jenssegers\Date\DateServiceProvider::class,
    Roumen\Feed\FeedServiceProvider::class,
    Thujohn\Twitter\TwitterServiceProvider::class,
    Collective\Html\HtmlServiceProvider::class,
		// ...
	],
```

Then, add the class aliases to the `aliases` array of `config/app.php`:

```
	'aliases' => [
		// ...
    // nonoesp/writing - Models
    'Writing' => Nonoesp\Writing\Facades\Writing::class,
    'User' => 'App\User',
    'Article' => Nonoesp\Writing\Models\Article::class,    
    'Recipient' => Nonoesp\Writing\Models\Recipient::class,

    // nonoesp/writing - Dependencies
    'Thinker' => Nonoesp\Thinker\Facades\Thinker::class,
    'Authenticate' => Nonoesp\Authenticate\Facades\Authenticate::class,
    'Date' => Jenssegers\Date\Date::class,
    'Feed' => Roumen\Feed\Feed::class,
    'Markdown'  => VTalbot\Markdown\Facades\Markdown::class,
    'Form' => Collective\Html\FormFacade::class,
    'Html' => Collective\Html\HtmlFacade::class,   
        'Input' => Illuminate\Support\Facades\Input::class,     

        'Twitter'   => Thujohn\Twitter\Facades\Twitter::class,
		// ...
	],
```

Finally, follow the [Instructions to Install `nonoesp/authenticate`](https://github.com/nonoesp/laravel-authenticate/tree/5.2)

## Config

There are some settings to customize the way you use the **Writing** package.

Even though you can publish the whole bundle of assets, I recommend to start by only publishing the config file and the required JavaScript assets.

First, run this command, and you will get a customizable config file under `config/writing.php`.

```php
php artisan vendor:publish --provider="Nonoesp\Writing\WritingServiceProvider" --tag=config
```

On the config/writing.php file you can customize:

* `use_path_prefix` — A boolean to toggle between using a path prefix for all the routes created by the package or deactivating it. Just set it to `true` or `false`.
* `path` — A string that defines your prefix path (only used if use_path_prefix is set to `true`).
* `protected_uris` — An array to avoid the Writing package to override other existing routes (specially when you set use_path_prefix to false).

## Gulp (And Elixir)

For dependencies on JavaScript, SCSS, and others, I’ve been working with gulp and elixir.

```
npm install gulp
```

Then to install it’s dependencies:

```
npm install
```

## Bower

To set the packages installed with bower to another directory, let’s add a `.bowerrc` file to the root directory with the following content:

```
{"directory" : "resources/assets/bower"}
```

Now we can run `bower install jquery` for instance.

To automate the movement of the files to the public folder let’s use a simple elixir script, add the following to your `gulpfile.js`:

```
elixir(function(mix) {
    mix.copy('resources/assets/bower/validatejs/validate.min.js', 'public/js/vendor/validate.min.js')
       .copy('resources/assets/bower/jquery/dist/jquery.min.js', 'public/js/vendor/jquery.min.js');
});
```

## To-dos

* Put user route inside controller.
* Add migrations to create database tables.
* Test Disqus implementation for comments.

## Features

* **Article recipients**. Make articles only visible to certain Twitter handles.

## Stylesheets

The package provides HTML structure but, for the moment being, it is CSS agnostic.

* c-article
* c-load-more
* o-wrap
* o-video-thumb

## Release Notes

### 5.2.*

* Added path-admin to config and set admin routes accordingly.
* Added explicit slugs.
* Embedded Article model inside package (fixed issue with rtconner/tagging not working when the model was hosted inside the package).
* Fixed routes: Tag, @user, RSS Feed


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