## Show an item outside of Folio

This sample route displays an item with an explicit `slug` with no need to have a matching route or domain in place.

```php
Route::get('/', function ($domain, Request $request) {
    return \Nonoesp\Folio\Controllers\FolioController::showItem($domain, $request, 'sketches');
});
```