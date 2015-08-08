# laravel-writing

A writing system for Laravel 4.

## Installation

Run `compose require nonoesp/writing:dev-master`

Add `'Nonoesp/Writing/WritingServiceProvider',` to `providers` in `/app/config/app.php`

## Dependencies

This package assumes your Laravel app is providing a `layout.main` view which is used as a view template, extended by laravel-writing. Also, the content of this package appears in the @section('content') section.

## SCSS Dependencies

* c-article
* c-load-more
* o-wrap
* o-video-thumb

## TODO

* Add assets inside package, make them publishable. At least `writing.js`.

# Notes++

*This should be passed later on to Notes-Laravel*

To use the controllers inside your own workbench/package, you need to manually add "src/controllers" to your classmap, and run `composer dump-autoload`. Then you are good to go. Make sure your controller extends `\BaseController` and not `BaseController`, and then use your controller as `Route::get('path', 'Vendor\Package\ControllerName@yourMethod');`.


## Publish Package Assets while Developing in the Workbench

The following command will copy your assets into `/public/packages/vendor/package/`. Development should be continued on the workbench. Then you can run the command again if you want to update the previously published assets.

`php artisan asset:publish --bench="vendor/package"`

## License

Thinker is licensed under the MIT license. (http://opensource.org/licenses/MIT)

## Me

I tweet at [@nonoesp](http://www.twitter.com/nonoesp) and blog at [nono.ma/says](http://nono.ma/says). If you use this package, I would love to hear about it. Thanks!