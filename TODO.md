
## TODO


- [ ] Template for page without header (like about).
- [ ] Select option for blog-or-not. (Pages and projects could be excluded from 'blog' feed.)

---

- Collections (maybe through custom proprties, 'collection-tags' : 'sketch')
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
