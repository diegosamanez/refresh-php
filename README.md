Refresh PHP
=================

Auto reload your web app automatically on save

Getting Started
---------------

```
$ composer require agregalel/refresh-php
```

### index.php file

```php
use agregalel\refresh_php\RefreshPhp;
/**
 * Init Refresh
 */
RefreshPhp::init();
```

You can also add a phprefresh.json configuration file to ignore files and directories

```json
{
    "ignore": [
        "vendor",
        "dir_ignore",
        "file_ignore.php"
    ]
}
```