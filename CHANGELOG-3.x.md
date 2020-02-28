# Release Notes for v3.x

This changelog references the relevant changes (bug and security fixes) done to `laravie/cabinet`.

## 3.0.1

Released: 2020-02-28

### Changes

* Add support for Laravel Framework v7.

## 3.0.0

Released: 2020-01-20

### Added

* Add `Laravie\Cabinet\Repository::share()` to register data to storage.
* Add `Laravie\Cabinet\Repository::put()` to set value to storage.
* Added `Laravie\Cabinet\Listeners\FlushCachedData` listener for `Illuminate\Auth\Events\Login` or `Illuminate\Auth\Events\Logout` event.

### Beaking Changes

* `Laravie\Cabinet\Repository::rememberForever()` now will register the data for cache key and return it's value instead of `Repository`.
* `Laravie\Cabinet\Repository::remember()` now will register the data for cache key and return it's value instead of `Repository`.

### Deprecated

* Deprecate `Laravie\Cabinet\Repository::register()`, use `share()` instead.
