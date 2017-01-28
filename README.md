# A Simple Blogging Package for Laravel

Hi there! `nonoesp/space` is a content management system package for Laravel.

This branch supports 5.3.

## Installation

Begin by installing this package through Composer. Edit your project’s `composer.json` file to require `nonoesp/space`.

```json
"require": {
	"nonoesp/space": "5.4.*"
}
```

Next, update Composer from the Terminal:

```
composer update
```

Next, add the new providers to the `providers` array of `config/app.php`:

```php
	'providers' => [
		// ...
        // nonoesp/space
        Nonoesp\Space\SpaceServiceProvider::class,        
        Nonoesp\Thinker\ThinkerServiceProvider::class,  
        Nonoesp\Authenticate\AuthenticateServiceProvider::class,          
        GrahamCampbell\Markdown\MarkdownServiceProvider::class,
        Conner\Tagging\Providers\TaggingServiceProvider::class,
        Jenssegers\Date\DateServiceProvider::class,
        Roumen\Feed\FeedServiceProvider::class,
        Thujohn\Twitter\TwitterServiceProvider::class,
        Collective\Html\HtmlServiceProvider::class,
        Vinkla\Hashids\HashidsServiceProvider::class,				
		// ...
	],
```

Then, add the class aliases to the `aliases` array of `config/app.php`:

```php
	'aliases' => [
		// ...
        // nonoesp/space - Models
        'Space' => Nonoesp\Space\Facades\Space::class,
        'User' => 'App\User',
        'Item' => Nonoesp\Space\Models\Item::class,    
        'Property' => Nonoesp\Space\Models\Property::class,
        'Recipient' => Nonoesp\Space\Models\Recipient::class,

        // nonoesp/space - Dependencies
        'Thinker' => Nonoesp\Thinker\Facades\Thinker::class,
        'Authenticate' => Nonoesp\Authenticate\Facades\Authenticate::class,
        'Date' => Jenssegers\Date\Date::class,
        'Feed' => Roumen\Feed\Feed::class,
        'Markdown' => GrahamCampbell\Markdown\Facades\Markdown::class,
        'Form' => Collective\Html\FormFacade::class,
        'Html' => Collective\Html\HtmlFacade::class,   
        'Input' => Illuminate\Support\Facades\Input::class,
        'Twitter'   => Thujohn\Twitter\Facades\Twitter::class,
        'Hashids'   => Vinkla\Hashids\Facades\Hashids::class,				
		// ...
	],
```

To authenticate, `Space` uses `nonoesp/authenticate` (which should be already installed as a dependency).
But you need to follow the [Instructions to Install `nonoesp/authenticate`](https://github.com/nonoesp/laravel-authenticate/tree/5.2)

## Config

Publish configuration file to `config/space.php`.

```php
php artisan vendor:publish --provider="Nonoesp\Space\SpaceServiceProvider" --tag=config
```

## Bower

Set bower's installation path in `.bowerrc` file, at your app's root:

```json
{"directory" : "resources/assets/bower"}
```

Now, add the following dependencies to your `bower.json`:

```json
"dependencies": {
	"core-scss": "git@github.com:nonoesp/core-scss.git",
	"jquery": "^3.1.1",
	"validatejs": "^1.5.1",
	"vue": "^2.1.8",
	"vue-resource": "^1.0.3",
	"font-awesome": "^4.7.0"
}
```

## Gulp (And Elixir)

For dependencies on JavaScript, SCSS, and others, I’ve been working with gulp and elixir.

```terminal
npm install gulp
```

Then to install it’s dependencies:

```terminal
npm install
```

Now, we automate the copying of the files to the public folder let’s use a simple elixir script, add the following to your `gulpfile.js`:

```php
elixir(mix => {
  mix.copy('resources/assets/bower/validatejs/validate.min.js', 'public/js/vendor/validate.min.js')
     .copy('resources/assets/bower/jquery/dist/jquery.min.js', 'public/js/vendor/jquery.min.js')
		 .copy('resources/assets/bower/vue/dist/vue.js', 'public/js/vendor/vue.js')
     .copy('resources/assets/bower/vue-resource/dist/vue-resource.js', 'public/js/vendor/vue-resource.js')
     .copy('resources/assets/bower/lodash/dist/lodash.min.js', 'public/js/vendor/lodash.min.js');

  mix.copy('resources/assets/bower/font-awesome/fonts', 'public/fonts/');

  mix.copy('vendor/nonoesp/space/resources/assets/js/space.main.js',  'public/js/space.main.js')
     .copy('vendor/nonoesp/space/resources/assets/js/space.admin.js', 'public/js/space.admin.js');

  //mix.sass('./vendor/nonoesp/space/resources/assets/sass/space.scss', 'public/css/space.css');

  mix.copy('./vendor/nonoesp/space/resources/assets/sass/', 'resources/assets/sass/');
  mix.sass('space.scss');
});
```

## License

Space is licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Me

I'm [Nono Martínez Alonso](http://nono.ma) (nono.ma), a computational designer with a penchant for design, code, and simplicity. I tweet at [@nonoesp](http://www.twitter.com/nonoesp) and write at [Getting Simple](http://gettingsimple.com/). If you use this package, I would love to hear about it. Thanks!
