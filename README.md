# laravel-writing
A writing system for Laravel 4.


## SCSS Dependencies

* c-article
* c-load-more
* o-wrap

## TODO

* Add assets inside package, make them publishable. At least `writing.js`.


## Notes

*This should be passed later on to Notes-Laravel*

To use the controllers inside your own workbench/package, you need to manually add "src/controllers" to your classmap, and run `composer dump-autoload`. Then you are good to go. Make sure your controller extends `\BaseController` and not `BaseController`, and then use your controller as `Route::get('path', 'Vendor\Package\ControllerName@yourMethod');`.


## Publish Package Assets while Developing in the Workbench

The following command will copy your assets into `/public/packages/vendor/package/`. Development should be continued on the workbench. Then you can run the command again if you want to update the previously published assets.

`php artisan asset:publish --bench="vendor/package"`