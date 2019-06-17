This Laravel commands help migrating old databases of Folio to the latest version.

```php
/*
 * A command to migrate subscribers to nonoesp/folio.
 */
Artisan::command('folio:subscribers', function () {
    
        $subscribers = \DB::table('subscribers')->orderBy('created_at', 'ASC')->get();
        
        foreach($subscribers as $s) {
            
            $subscriber = new Subscriber();
            $subscriber->created_at = $s->created_at;
            $subscriber->updated_at = $s->updated_at;    
            $subscriber->email = $s->email;
            // $subscriber->deleted_at = $s->deleted_at;    
            // $subscriber->name = $s->name;
            // $subscriber->source = $s->source;
            // $subscriber->campaign = $s->campaign;
            // $subscriber->path = $s->path;
            // $subscriber->ip = $s->ip;
            $subscriber->save();
            //$this->info($s->email);
        }
    
        $this->comment('Migrated '.count($subscribers). ' subscribers.');
    });

/*
 * A command to migrate an nonoesp/writing database to nonoesp/folio.
 * Basically, converts the information from Articles to Items.
 * (Refer to the retag method for retagging the new Items.)
 */
Artisan::command('folio:articles {truncate=0}', function($truncate) {
    
    if($truncate) {
        $this->info(Item::withTrashed()->count().' existing Items were removed.');
        Item::truncate();
    } else {
        $this->info("Keeping ".Item::withTrashed()->count()." existing Items.");
    }

    // Get all existing old articles ordered by publication date
    $articles = DB::table('articles')->orderBy('published_at', 'ASC')->get();
    $items = [];
    
    foreach($articles as $article) {

        //$this->info($article->id.'. '.$article->title .' - '.$article->tags_str);
        
        $item = new Item();
        $item->id = $article->id;
        $item->created_at = $article->created_at;
        $item->published_at = $article->published_at;
        $item->deleted_at = $article->deleted_at;
        $item->user_id = $article->user_id;
        $item->title = $article->title;
        $item->text = $article->text;
        $item->rss = $article->rss;
        $item->tags_str = $article->tags_str;
        $item->slug = $article->slug;
        $item->visits = $article->visits;

        if($image = $article->image) {
            $item->image = $image;
        }

        if($video = $article->video) {
            $item->video = $video;
        }

        if($image_src = $article->image_src) {
            $item->image_src = $image_src;
        }

        //$item->link = $article->link;
        $item->save();

        array_push($items, $item);
    }

    $this->info(count($items).' were created.');
});

/*
 * A command to retag existing Folio Items.
 */
Artisan::command('folio:retag', function() {
    
    $items = Item::withTrashed()->get();
    foreach($items as $item) {
        if($tags_str = $item->tags_str) {
            $tags = explode(",", $tags_str);
            $this->info('Item has '.count($tags).' - '.$tags_str);
            $item->retag($tags);
            $item->save();
        }
    }

});
```

# 2019.06.04 Translations

How to update to translatable models.

## Install

This is done automatically because the dependency is in `composer.json`.

```bash
# composer require spatie/laravel-translatable
```

## Database

- rename `title` to `title_plain`
- rename `text` to `text_plain`
- create a `title` field that is of JSON type in database table
- create a `text` field that is of JSON type in database table

## Run migration command

Put this command in your Laravel app, in `routes/console.php`.

```php
/*
 * A command to migrate Folio Item's title and text
 * to JSON to support the HasTranslations trait.
 */
Artisan::command('folio:toJSON', function() {
    
    $items = Item::withTrashed()->get();
    app()->setLocale('en');
    foreach($items as $item) {
        $this->info("Migrated item $item->id.");
        $item->title = $item->title_plain;
        $item->text = $item->text_plain;
        $item->save();
    }
});
```

Then run the following command on the terminal.

```bash
php artisan folio:toJSON
```

## Files edited

- `composer.json`
- `config/config.php`
- `src/models/Item.php`
- `src/controllers/AdminController.php`
- `resources/views/admin/item-edit.blade.php`
- `resources/views/admin/item-list.blade.php`

## Export item properties

```php
Route::get('export/{id}', function($id) {
  // Get item
  $item = Item::find($id);
  // Encode properties as JSON
  $json = json_encode($item->properties);
  // Save file as props.json
  $filename = $item->id.'.json';
  if ($item->hasProperty('export-name')) {
    filename = $item->stringProperty('export-name');    
  }
  Storage::put($filename, $json);
});
```

## Import item properties

```php
// Load item properties from JSON
$contents = File::get(storage_path('app/props.json'));
// Add each property to item with provided id
foreach(json_decode($contents) as $prop) {
    $property = new Property();
    $property->item_id = $id;
    $property->label = $prop->label;
    $property->name = $prop->name;
    $property->value = $prop->value;
    $property->save();
}
```