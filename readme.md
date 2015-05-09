### Installation

Use Laravel 5 Blade templating engine as a standalone component

```
$ composer require txthinking/blade
```

### Usage

```
<?php
use \Tx\ViewProvider;

$v = (new ViewProvider([
    'viewPaths' => [__DIR__ . '/views'],
    'cachePath' => __DIR__ . '/cache',
]))->getInstance();

echo $v->make('hello');
```

### Documentation

http://laravel.com/docs/5.0/templates
