# Release Notes for v2.x

This changelog references the relevant changes (bug and security fixes) done to `laravie/cabinet`.

## 2.2.0

Released: 2019-04-15

### Added

* Added `Laravie\Cabinet\Repository::fresh()` helper method to forget knwon key and get fresh value from storage.

### Changes

* Remove support for Laravel Framework v5.7 and below.
* Rename `$duration` to `$ttl`.

## 2.1.2

Released: 2019-03-29

### Changes

* Improve performance by prefixing all global functions calls with `\` to skip the look up and resolve process and go straight to the global function.

## 2.1.1

Released: 2018-09-13

### Changes

* Trivia update library setup.

## 2.1.0

Released: 2018-07-31

### Changes

* Bump minimum PHP version to 7.1.
* Bump minimum Laravel Framework to 5.6.+.

## 2.0.3

Released: 2018-07-30

### Fixes

* Fixes compatibility with PHP 7.0.+.

## 2.0.2

Released: 2018-07-30

### Changes

* Flush data from persistent cache if it is corrupted.

## 2.0.1

Released: 2018-05-02

### Changes

* Return `self` should only be used when method is marked as `final`.

## 2.0.0

Released: 2018-01-22

### Added

* Added `Laravie\Cabinet\Contracts\Storage` contract.
* Added `Laravie\Cabinet\Item` class.

### Changes

* Bump minimum PHP version to 7.1.
* Refactors `Laravie\Cabinet\Repository`.
