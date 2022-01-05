# Change Log


All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).



## [3.3.0] - 2022-01-05

### Changed

- Support PHP 8.0


## [3.2.0] - 2021-09-27

### Changed

- Dropped PHP7.3 support
- Upgraded lcobucci/jwt dependency to version 4

## [3.1.0] - 2021-07-26

### Changed

- Dropped PHP7.2 support
- Guzzle dependency upgrade to 7


## [3.0.3] - 2021-02-10

### Fixed

- Fix exception conversion


## [3.0.2] - 2021-02-10

### Fixed

- Fix PreventTooManyProviderTokenUpdates


## [3.0.1] - 2020-11-06

### Fixed

- Fix bug kid must header


## [3.0.0] - 2020-05-27

### Added

- Apple APN Token based sender
- PHP 7.4 support

### Removed

- Removed Apple APN certificate generation


## [2.0.0] - 2018-11-27

### Added

- Firebase Cloud Messaging

### Changed

- PHP requires version 7.1+
- Strict types
- Apple\PushCertificate is now called Apple\CombinedCertificate

### Removed

- Remove Windows
