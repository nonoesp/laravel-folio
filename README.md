<p>
<img src="assets/folio-dark@2x.gif#gh-dark-mode-only" alt="Folio for Laravel logo." width="138">
<img src="assets/folio-light@2x.gif#gh-light-mode-only" alt="Folio for Laravel logo." width="138">
</p>

A customizable Laravel content-management system.

You can see it working at [Nono.MA](https://nono.ma), [Getting Simple](https://gettingsimple.com), [Burns.art](https://burns.art), [RCA Media Studies](https://ms.rca-architecture.com), or [Luis Ruiz Padrón](https://luisruiz.es).

## Installation · Laravel 9.x

- Add alternate VCS repos for packages without Laravel 9.x support to `composer.json`.

```json
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/nonoesp/laravel-imgix"
        },
        {
            "type": "vcs",
            "url": "https://github.com/macpaw/laravel-feed"
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
- Backups can be configured by adding disks to `backup.destination.disks` (having those disks configured in `filesystems`, say [Dropbox](https://www.dropbox.com/developers/apps), DigitalOcean, or AWS S3).

## License

Folio is licensed under the [MIT license](http://opensource.org/licenses/MIT).

## Me

Hi. I'm [Nono Martínez Alonso](https://nono.ma/about) (Nono.MA), a creative technologist with a penchant for simplicity.

I host [Getting Simple](https://gettingsimple.com) — a podcast about simple living, lifestyle design, technology, and culture — [sketch](https://sketch.nono.ma) things that call my attention, [write](https://gettingsimple.com/writing) about enjoying a slower life, and record creative coding and machine intelligence [live streams](https://youtube.com/NonoMartinezAlonso) on YouTube.

[Join us on Discord](https://nono.ma/discord). 🗣

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
📸 [YouTube](https://youtube.com/NonoMartinezAlonso)
