# laravel-writing
A writing system for Laravel 4.


## Notes

To use the controllers inside your own workbench/package, you need to manually add "src/controllers" to your classmap, and run `composer dump-autoload`. Then you are good to go. Make sure your controller extends `\BaseController` and not `BaseController`, and then use your controller as `Route::get('path', 'Vendor\Package\ControllerName@yourMethod');`.