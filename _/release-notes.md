
## Release Notes

### 8.x

- Add Google Analytics 4 support.
- `TODO`

### 7.x

- Added `folio:install` command.
- `TODO`

### 5.8.*

- Add item translations (including properties).
- Add per-item specific domain (and `domain-pattern-items`).
- `protected_uris` → `reserved-uris`.
- Add tag-stripping to `htmlText` (and convert parameters to `$options` Array).

### 5.5.*

- Add package auto-discovery.

### 5.4.*

* Renamed package to `nonoesp/space`.
* Abandoned `vtalbot/markdown` and switched to `graham-campbell/markdown`.
* Transferred development to `master` branch.
* Added migrations for database tables.
* Added complete installation instructions.
* Moved `Subscriber` model and route to package.
* Added Vue interactions to admin.
* Renamed `Article` class to `Item` to make it more generic.
* Implemented `core-scss` as the main style for Sass.
* Added compiled resources for direct publishing.
* Added un-compiled Sass and JavaScript resources with compiling workflow for customization.
* Now you can add custom properties to an `Item`.

### 5.3.*

* Customizable short-URLs included in package routes.

### 5.2.*

* Added path-admin to config and set admin routes accordingly.
* Added explicit slugs.
* Embedded Article model inside package (fixed issue with rtconner/tagging not working when the model was hosted inside the package).
* Fixed routes: Tag, @user, RSS Feed.


### v0.8.0

* Added article recipients. Add a list of Twitter handles to your article to restrict their visibility to those users when they are logged in. Otherwise, the site just shows visitor posts with no recipients.

### v0.7.2

* Introduced special tag CSS class names. A class like c-article-tagName will be added to a series of special tags or categories when a post contains them. This allows to create custom CSS for posts tagged with an specific tag.

### v0.7.1

* Selective routes only if slug exists.
