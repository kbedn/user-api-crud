##Simple User CRUD
Installation:

Create a local clone of Github kbedn/crud

Browse the project directory and execute 'composer install' [Installing Composers](https://getcomposer.org/download/).

After composer manage dependencies, cache and base parameters, please execute following commands:

```
bin/console d:d:c
bin/console d:s:u --force
bin/console se:ru
```

Then, open your browser and access the http://localhost:8000/user to run simple user crud site.