##Simple User api CRUD - telemedico
Installation:

Create a local clone of Github kbedn/user-api-crud

Browse the project directory and execute 'composer install' [Installing Composers](https://getcomposer.org/download/).

After composer manage dependencies, cache and base parameters, please execute following commands:

```
bin/console d:d:c
bin/console d:s:u --force
bin/console se:ru
```

Then, open your browser and access the http://127.0.0.1:8000/ run simple user crud site.
Routing available via

```
bin/console debug:router
```
