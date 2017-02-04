
## TODO

* Move user route inside controller.
* Test Disqus implementation for comments.
* Config option to redirect from `/` to `Space::path()`.


## Add to README.md

```
foreach(Space::itemPropertiesWithPrefix($item, 'p') as $property) {
    echo '<li><strong>'. $property->label .'</strong> — '. $property->value .'</li>';
}
```

(And the blade equivalent?)
