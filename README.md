<img src="assets/folio@2x.gif?reload2" alt="Folio for Laravel logo." width="200px">

Display your web content with custom templates.

## Live Examples

- [Nono.MA](https://nono.ma)
- [Getting Simple](https://gettingsimple.com)
- [Lourdes.AC](https://lourdes.ac)
- [AR-MA](https://ar-ma.net)
- [Getting Architecture Done](http://gettingarchitecturedone.com/writing)
- [Nacho.MA](https://nacho.ma)

## Installation

Require using Composer:

```bash
composer require nonoesp/folio:dev-master
```

Most packages should be auto-discovered by Laravel.

```bash
php artisan folio:install
```

- `TODO` List things this command does for you.

### Middleware

Add the following middleware to `app/Http/Kernel.php`:

```php
    protected $middlewareGroups = [
        'web' => [
            /// nonoesp/folio
            \Nonoesp\Folio\Middleware\SetLocales::class,
        ],
    ];
```

### Migrations

- Remove Laravel's default migration files from `database/migrations` as they can collide with Folio's migrations. (`TODO` - Check if this is necessary or Folio's `users` table works.)
- Setup your database connection in the `.env` file.
- Publish `rtconner/tagging` migrations:

```bash
php artisan vendor:publish --provider="Conner\Tagging\Providers\TaggingServiceProvider"
```

- Run the migrations

```bash
php artisan migrate --path=vendor/mpociot/versionable/src/migrations
php artisan migrate
```

*Note: Revert migrations with `php artisan migrate:reset`, but tables and contents will be removed.*

## Configuration

Folio supports optional configuration.

To get started, publish the configuration file to `config/folio.php`.

```bash
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=config
```

## Compile Web Assets

- Publish the assets with:

```bash
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=dev-assets
```

This will copy Folio stylesheets and JavaScript files to the resources folder, in `sass` and `js`, respectively.

- Copy the following code into `webpack.mix.js`.

```javascript
const mix = require('laravel-mix');

mix.sass('resources/sass/folio.scss', 'public/folio/css')
// .sourceMaps()
   .js('resources/js/folio.js', 'public/folio/js')
   .extract([
       'vue',
       'vue-resource',
       'vue-focus',
       'jquery',
       'jquery-lazy',
       'validate-js',
       'lodash',
       'axios'
   ]);

// Use versioning and cache busting in production (i.e. npm run prod)
if (mix.inProduction()) {
   mix.version();
}

// mix.copy('node_modules/folio-scss/vendor/icons-links-gwern', 'public/folio/icons');

// BrowserSync when watching (i.e. npm run watch)
// mix.browserSync('localhost:8000');
```

- Install dependencies with `npm`.

```bash
npm install nonoesp/folio-scss bourbon@4.3.4 font-awesome vue vue-resource vue-focus lodash jquery jquery-lazy validate-js vuedraggable
npm install
```

- Build with:
  - `npm run prod` · to build for production
  - `npm run dev` · to build for development
  - `npm run watch` · to rebuild on changes with BrowserSync

## Publish Translations

```bash
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=lang
```

## Publish Views

```bash
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=views
```

## Subscribers Notifications

Optionally, you can receive new subscriber notifications via email.

- Setup Amazon SES in `config/services.php`:

```php
	// Amazon SES
	'ses' => [
        'key' => 'your-api-key',
    	'secret' => 'your-secret',
    	'region' => 'us-east-1',
	],
```

- The activate notifications in `config/folio.php`:

```php
'subscribers' => [
    'should-notify' => true,
    'from' => [
        'email' => 'from@email.com',
        'name' => 'John McFrom'
    ],
    'to' => [
        'email' => ['to@email.com'],
        'name' => ['Jane McTo']
    ]
],
```

## Backups

Folio uses `spatie/laravel-backup` to do full app backups containing a database dump and/or all local files.

- Obtain an authorization token at https://www.dropbox.com/developers/apps
- Add the `dropbox` filesystem.
- Publish

```php
        'dropbox' => [
            'driver' => 'dropbox',
            'authorization_token' => 'your-token'
        ],
```

## License

Folio is licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Me

Hi. I'm [Nono Martínez Alonso](https://nono.ma/about) (Nono.MA), a computational designer with a penchant for simplicity.

I host [Getting Simple](https://gettingsimple.com)—a podcast about how you can live a meaningful, creative, simple life—[sketch](https://sketch.nono.ma) things that call my attention, and [write](https://gettingsimple.com/writing) about enjoying a slower life.

If you find Folio useful in any way, reach out on Twitter at [@nonoesp](https://twitter.com/nonoesp). Cheers!
