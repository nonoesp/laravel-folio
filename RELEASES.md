
## To-dos

* Move Subscriber model and route to package.
* Move user route inside controller.
* Add migrations to create database tables.
* Test Disqus implementation for comments.

## Release Notes

### 5.4.*

* Renamed package to `nonoesp/space`.

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
