
## TODO

- [ ] Add item translations in admin.
- [ ] Add `info` template for page without header (like about) from `nono.ma`.
- [ ] Select option for blog-or-not. (Pages and projects could be excluded from 'blog' feed.) Maybe categories?
- [ ] amazon set option, notification of new subscribers via e-mail

---

- Collections (maybe through custom properties, 'collection-tags' : 'sketch')
* Move user route inside controller. (Research how default Laravel model works.)
* Test Disqus implementation for comments.
* Config option to redirect from `/` to `Space::path()`.

## Add to README.md

```
foreach(Space::itemPropertiesWithPrefix($item, 'p') as $property) {
    echo '<li><strong>'. $property->label .'</strong> — '. $property->value .'</li>';
}
```

(And the blade equivalent?)

## Usability

- Admin (better way to arrange and filter items)
- Admin ajax functionality (e.g. activate-deactivate that can be also activate in templates)
- Permalink more explicit (visible in all pages and admin)

## DONE

170326
- [X] explicit slug
  - relative `this-is-slug` produces `site.com/blog/this-is-slug` (useful for posts, essays, etc.)
  - absolute `/this-is-slug` produces `site.com/this-is-slug` (useful for projects and collections), we could probably also use `/writing/this-is-slug` to produce `site.com/writing/this-is-slug`
- [x] adjust layouts for $item->path() support

170323
- [x] Template directories (search for templates in default and custom)
- [x] Properties callback check matches current state. One call for each field.
- [x] Space: Rename Article class to Item.
- [x] Space: Rename $property→value to $property→key (key, reserved SQL word)
- [x] Space: field name label
- [x] Space: Fix footnotes spacing.
- [x] Space: Expose admin header options in config. (Like normal header.)
- [x] Space: Upgrade to 5.4 (in-process)
- [x] Space: Properties. (Custom properties handled with Vue.js)

## Migration Scripts

### Move Items from `articles` to `folio_items`

```php
// Route::get('migrate', function() {
//   $articles = DB::table('articles')->orderBy('published_at', 'ASC')->get();
//   foreach($articles as $article) {
//     echo $article->title .' - '.$article->tags_str;
//     $item = new Item();
//     $item->created_at = $article->created_at;
//     $item->published_at = $article->published_at;
//     $item->deleted_at = $article->deleted_at;
//     $item->user_id = $article->user_id;
//     $item->title = $article->title;
//     $item->text = $article->text;
//     if($article->image) $item->image = $article->image;
//     if($article->video) $item->video = $article->video;
//     $item->tags_str = $article->tags_str;
//     $item->slug = $article->slug;
//     $item->visits = $article->visits;
//     $item->link = $article->link;
//     $item->save();
//
//     if(substr($item->tags_str,0,1) == ',') {
//
//       $tags_str = substr($item->tags_str,1,strlen($item->tags_str)-2);
//       $tag_ids = explode(",", $tags_str);
//       $tag_names = [];
//
//       foreach($tag_ids as $tag_id) {
//         if($tag_id == "1") { array_push($tag_names, "design"); }
//         else if($tag_id == "2") { array_push($tag_names, "code"); }
//         else if($tag_id == "3") { array_push($tag_names, "productivity"); }
//         else if($tag_id == "4") { array_push($tag_names, "laravel"); }
//         else if($tag_id == "5") { array_push($tag_names, "web"); }
//         else if($tag_id == "6") { array_push($tag_names, "architecture"); }
//         else if($tag_id == "7") { array_push($tag_names, "link-pack"); }
//         else if($tag_id == "8") { array_push($tag_names, "friday link pack"); }
//         else if($tag_id == "9") { array_push($tag_names, "gad"); }
//         else if($tag_id == "10") { array_push($tag_names, "dribbble"); }
//         else if($tag_id == "11") { array_push($tag_names, "videos"); }
//         else if($tag_id == "12") { array_push($tag_names, "geometry"); }
//         else if($tag_id == "13") { array_push($tag_names, "video"); }
//         else if($tag_id == "14") { array_push($tag_names, "quotes"); }
//         else if($tag_id == "15") { array_push($tag_names, "article"); }
//         else if($tag_id == "16") { array_push($tag_names, "getting architecture done"); }
//         else if($tag_id == "17") { array_push($tag_names, "ios"); }
//         else if($tag_id == "18") { array_push($tag_names, "kickstarter"); }
//         else if($tag_id == "19") { array_push($tag_names, "git"); }
//         else if($tag_id == "20") { array_push($tag_names, "grasshopper"); }
//         else if($tag_id == "21") { array_push($tag_names, "python"); }
//         else if($tag_id == "22") { array_push($tag_names, "rhino"); }
//         else if($tag_id == "23") { array_push($tag_names, "vine"); }
//         else if($tag_id == "24") { array_push($tag_names, "espacio"); }
//         else if($tag_id == "25") { array_push($tag_names, "markdown"); }
//         else if($tag_id == "26") { array_push($tag_names, "writing"); }
//         else if($tag_id == "27") { array_push($tag_names, "made me look"); }
//         else if($tag_id == "28") { array_push($tag_names, "marketing"); }
//         else if($tag_id == "29") { array_push($tag_names, "art"); }
//         else if($tag_id == "30") { array_push($tag_names, "scripting"); }
//         else if($tag_id == "31") { array_push($tag_names, "photoshop"); }
//         else if($tag_id == "32") { array_push($tag_names, "workflows"); }
//         else if($tag_id == "33") { array_push($tag_names, "type"); }
//         else if($tag_id == "34") { array_push($tag_names, "autocad"); }
//         else if($tag_id == "35") { array_push($tag_names, "derivasia"); }
//         else if($tag_id == "36") { array_push($tag_names, "viewtee"); }
//         else if($tag_id == "37") { array_push($tag_names, "book"); }
//         else if($tag_id == "38") { array_push($tag_names, "lettering"); }
//         else if($tag_id == "39") { array_push($tag_names, "ted"); }
//         else if($tag_id == "40") { array_push($tag_names, "indesign"); }
//         else if($tag_id == "41") { array_push($tag_names, "technology"); }
//         else if($tag_id == "42") { array_push($tag_names, "amazon"); }
//         else if($tag_id == "43") { array_push($tag_names, "longform"); }
//         else if($tag_id == "44") { array_push($tag_names, "internet"); }
//         else if($tag_id == "45") { array_push($tag_names, "entrepreneur"); }
//         else if($tag_id == "46") { array_push($tag_names, "facebook"); }
//         else if($tag_id == "47") { array_push($tag_names, "work"); }
//         else if($tag_id == "48") { array_push($tag_names, "life"); }
//         else if($tag_id == "49") { array_push($tag_names, "minimalism"); }
//         else if($tag_id == "50") { array_push($tag_names, "apple"); }
//         else if($tag_id == "51") { array_push($tag_names, "ipad"); }
//         else if($tag_id == "52") { array_push($tag_names, "apps"); }
//         else if($tag_id == "53") { array_push($tag_names, "everfocus"); }
//         else if($tag_id == "54") { array_push($tag_names, "php"); }
//         else if($tag_id == "55") { array_push($tag_names, "terminal"); }
//         else if($tag_id == "56") { array_push($tag_names, "os x"); }
//         else if($tag_id == "57") { array_push($tag_names, "twitter"); }
//         else if($tag_id == "58") { array_push($tag_names, "news"); }
//         else if($tag_id == "59") { array_push($tag_names, "organization"); }
//         else if($tag_id == "60") { array_push($tag_names, "travel"); }
//         else if($tag_id == "61") { array_push($tag_names, "vagabonding"); }
//         else if($tag_id == "62") { array_push($tag_names, "smartphone"); }
//         else if($tag_id == "63") { array_push($tag_names, "parametric"); }
//         else if($tag_id == "64") { array_push($tag_names, "mini-story"); }
//         else if($tag_id == "65") { array_push($tag_names, "entrepreneurship"); }
//         else if($tag_id == "66") { array_push($tag_names, "made-me-think"); }
//         else if($tag_id == "67") { array_push($tag_names, "android"); }
//         else if($tag_id == "68") { array_push($tag_names, "efficiency"); }
//         else if($tag_id == "69") { array_push($tag_names, "books"); }
//         else if($tag_id == "70") { array_push($tag_names, "library"); }
//         else if($tag_id == "71") { array_push($tag_names, "focus"); }
//         else if($tag_id == "72") { array_push($tag_names, "time"); }
//         else if($tag_id == "73") { array_push($tag_names, "getting simple"); }
//         else if($tag_id == "74") { array_push($tag_names, "simplicity"); }
//         else if($tag_id == "75") { array_push($tag_names, "scarcity"); }
//         else if($tag_id == "76") { array_push($tag_names, "uniform"); }
//         else if($tag_id == "77") { array_push($tag_names, "talk"); }
//         else if($tag_id == "78") { array_push($tag_names, "tutorial"); }
//         else if($tag_id == "79") { array_push($tag_names, "disconnect"); }
//         else { array_push($tag_names, $tag_id); }
//       }
//
//       $new_tags_str = '';
//       $i = 0;
//       foreach($tag_names as $tag_name) {
//         if($i>0) $new_tags_str .= ', ';
//         $new_tags_str .= $tag_name;
//         $i++;
//       }
//
//
//       echo ' --- new_tag string: '.$new_tags_str;
//
//       $item->tags_str = $new_tags_str;
//       $item->save();
//     }
//
//
//     if ($item->tags_str != '') {
//
//       $item->tag(explode(",", $item->tags_str));
//
//     }
//
//     echo '<br><br>';
//
//   }
// });
```

### Retag?

```
// Route::get('/tags', function() {
//   $tags = DB::table('tagging_tags')->orderBy('id', 'ASC')->get();
//   foreach($tags as $tag) {
//     echo ' else if($tag_id == "'.$tag->id.'") {
//       array_push($tag_names, "'.$tag->name.'");
//     }';
//     echo '<br>';
//   }
// });
```