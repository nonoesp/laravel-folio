
## TODO


- [ ] lazy load for images (in collections)!
- [ ] fix footnotes in rss (use older markdown parser like nonoma)
- [ ] next/previous (of tag) category (based on source url, portraits or writing for instance)

---

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