## Show an item outside of Folio

This sample route displays an item with an explicit `slug` with no need to have a matching route or domain in place.

```php
Route::get('/', function ($domain, Request $request) {
    return \Nonoesp\Folio\Controllers\FolioController::showItem($domain, $request, 'sketches');
});
```

## Show ip and host

```php
Route::get('ip', function() {
  $ip = \Request::server('SERVER_ADDR');
  $name = 'localhost';
  switch ($ip) {
    case '205.186.161.242':
      $name = 'Media Temple';
    break;
    case '142.93.41.147';
      $name = 'Digital Ocean';
    break;
    default:
      break;
  }
  return view('errors.layout', ['headline' => $name, 'text' => $ip]);
})->middleware('auth');
```