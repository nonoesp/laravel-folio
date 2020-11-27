<img src="assets/folio@2x.gif?reload2" alt="Folio for Laravel logo." width="200px">

A customizable Laravel content-management system. (Currently in beta.)

## Live Examples

[Nono.MA](https://nono.ma) | [Getting Simple](https://gettingsimple.com) | [Lourdes.AC](https://lourdes.ac) | [AR-MA](https://ar-ma.net) | [Getting Architecture Done](http://gettingarchitecturedone.com/writing) | [Nacho.MA](https://nacho.ma) | [Burns.art](https://burns.art) | [RCA Media Studies](https://ms.rca-architecture.com) | [Luis Ruiz Padrón](https://luisruiz.es)

## Installation · Laravel 8.x

- Add alternate VCS repos for packages without Laravel 8 support to `composer.json`.

```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/nonoesp/laravel-imgix"
        }
    ],
```

- `composer require nonoesp/folio:dev-master`
- `php artisan folio:install`
- `php artisan migrate`
- `php artisan migrate --path=vendor/mpociot/versionable/src/migrations`
- Add the following middleware to `app/Http/Kernel.php`:

```php
    protected $middlewareGroups = [
        'web' => [
            /// nonoesp/folio
            \Nonoesp\Folio\Middleware\SetLocales::class,
        ],
    ];
```

- Party! 🥳

## Build Folio's Assets with Laravel Mix

You can fully customize the JavaScript and SCSS assets.

- Publish Folio's assets
    - `php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=dev-assets`
- Install npm dependencies
    - `npm install nonoesp/folio-scss font-awesome vue vue-resource vue-focus lodash jquery jquery-lazy jquery-unveil validate-js vuedraggable @wordpress/wordcount animate.css`
    - `npm install`
- Build the assets with Laravel Mix and configurate it with `webpack.mix.js`
    - `npm run prod` · to build for production
    - `npm run dev` · to build for development
    - `npm run watch` · to rebuild on changes with BrowserSync

## Customize Folio's Config, Views, Translations & Assets

Configure Folio by publishing `config/folio.php`.

```bash
# Config
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=config

# Views
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=views

# Translations
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=lang

# JavaScript & SCSS assets
php artisan vendor:publish --provider="Nonoesp\Folio\FolioServiceProvider" --tag=dev-assets
```

## Other

- Subscriber email notifications can be configured in `folio.subscribers` and setting up Amazon SES in `services.ses`.
- Backups can be configured by adding disks to `backup.destination.disks` (having those disks configured in `filesystems`, say [Dropbox](https://www.dropbox.com/developers/apps), Digital Ocean, or S3).

## License

Folio is licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Me

Hi. I'm [Nono Martínez Alonso](https://nono.ma/about) (Nono.MA), a computational designer with a penchant for simplicity.

I host [Getting Simple](https://gettingsimple.com)—a podcast about how you can live a meaningful, creative, simple life—[sketch](https://sketch.nono.ma) things that call my attention, [write](https://gettingsimple.com/writing) about enjoying a slower life, and recently started [live streaming and recording videos](https://youtube.com/NonoMartinezAlonso) on machine learning, life hacking, and more.

[Join the Discord community](https://nono.ma/discord).

If you find Folio useful in any way, reach out on Twitter at [@nonoesp](https://twitter.com/nonoesp). Cheers!

## Elsewhere

🍃 [Getting Simple](https://gettingsimple.com)  
🎙 [Podcast](https://gettingsimple.com/podcast)  
🗣 [Ask Questions](https://gettingsimple.com/ask)  
💬 [Discord](https://discord.gg/DdsefVZ)  
👨🏻‍🎨 [Sketches](https://sketch.nono.ma)  
✍🏻 [Blog](https://nono.ma)  
🐦 [Twitter](https://twitter.com/nonoesp)  
📸 [Instagram](https://instagram.com/nonoesp)
