# Changelog

This changelog references the relevant changes (bug and security fixes) done to `laravie/cabinet`.

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

## 1.0.0

Released: 2017-11-07

### Added

* Initial stable release.
