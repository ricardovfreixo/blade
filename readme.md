### Installation

Use Laravel 5 Blade templating engine as a standalone component

```
$ composer require txthinking/blade
```

### Usage

```
<?php
use Tx\Blade;

$views = __DIR__ . '/views';
$cache = __DIR__ . '/cache';

$blade = new Blade($views, $cache);
$view = $blade->getView();

echo $view->make('hello');
```

### Documentation

http://laravel.com/docs/5.0/templates
