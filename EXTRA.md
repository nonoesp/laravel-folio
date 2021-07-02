## Show an item outside of Folio

This sample route displays an item with an explicit `slug` with no need to have a matching route or domain in place.

```php
Route::get('/', function ($domain, Request $request) {
    return \Nonoesp\Folio\Controllers\FolioController::showItemBySlug($domain, $request, 'sketches');
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

## Load domain-specific .env file if it exists

```php
/*
|--------------------------------------------------------------------------
| Load domain-specific .env file if it exists
|--------------------------------------------------------------------------
*/

if(isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])){
    $domain = $_SERVER['HTTP_HOST'];
} //

if (isset($domain)) {
    $dotenv = \Dotenv\Dotenv::create(base_path(), '.env.'.$domain);
    try {
        $dotenv->overload();
    } catch (\Dotenv\Exception\InvalidPathException $e) {
        // No custom .env file found for this domain
    }
}
```