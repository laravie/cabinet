Runtime Cache for Laravel Eloquent
==============

Cabinet allows you to attach runtime or persistent caching to any Laravel Eloquent instance.

[![Build Status](https://travis-ci.org/laravie/cabinet.svg?branch=master)](https://travis-ci.org/laravie/cabinet)
[![Latest Stable Version](https://poser.pugx.org/laravie/cabinet/v/stable)](https://packagist.org/packages/laravie/cabinet)
[![Total Downloads](https://poser.pugx.org/laravie/cabinet/downloads)](https://packagist.org/packages/laravie/cabinet)
[![Latest Unstable Version](https://poser.pugx.org/laravie/cabinet/v/unstable)](https://packagist.org/packages/laravie/cabinet)
[![License](https://poser.pugx.org/laravie/cabinet/license)](https://packagist.org/packages/laravie/cabinet)

## Installation

To install through composer, simply put the following in your `composer.json` file:

```json
{
    "require": {
        "laravie/cabinet": "^3.0"
    }
}
```

And then run `composer install` or `composer update` from the terminal.

### Quick Installation

Above installation can also be simplify by using the following command:

    composer require "laravie/cabinet=^3.0"

## Usages

### Setup Cabinet on a Model

You first need to add `Laravie\Cabinet\Cabinet` on an Eloquent Model such as:

```php
<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravie\Cabinet\Cabinet;

class User extends Authenticatable
{
    use Cabinet;
}
```

#### Allow persistent caching

To add persistent caching on the Eloquent, you have to attach a cache storage that support tags (Cache tags are not supported when using the `file` or `database` cache driver).

```php
<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravie\Cabinet\Cabinet;

class User extends Authenticatable
{
    use Cabinet;

    /**
     * Configure cabinet for the eloquent model.
     * 
     * @param  \Laravie\CabinetRepository  $cabinet 
     * @return void
     */
    protected function onCabinet($cabinet)
    {
        $cabinet->setStorage(resolve('cache.store'));
    }
}
```

### Storing data

#### Runtime

```php
Laravie\Cabinet\Repository::share(string $key, callable $callback);
```

The method allows a value to be register for `$key` using a closure/callable `$callback`.

```php
$user->cabinet()->share('birthday', static function ($user) {
    return now()->diffInDays($user->birthdate);
});
```

### Persistent with TTL

```php
Laravie\Cabinet\Repository::share(string $key, callable $callback, $ttl = null);
```

By adding the 3rd parameter `$ttl` (in seconds), Cabinet will attempt to store the data in cache for `$ttl` seconds.

```php
$user->cabinet()->share('birthday', static function ($user) {
    return now()->diffInDays($user->birthdate);
}, 60);
```

#### Forever

```php
Laravie\Cabinet\Repository::forever(string $key, callable $callback);
```

You can either use `forever` as the 3rd parameter using `share` or use `forever` to cache the value indefinitely.

```php
$user->cabinet()->share('birthday', static function ($user) {
    return now()->diffInDays($user->birthdate);
}, 'forever');

// or

$user->cabinet->forever('birthday', static function ($user) {
    return now()->diffInDays($user->birthdate);
})
```

### Retrieving the data

```php
Laravie\Cabinet\Repository::get(string $key);
```

Retrieving the data using `get` method.

```php
$user->cabinet()->get('birthday');

// or

$user->cabinet('birthday');
```
