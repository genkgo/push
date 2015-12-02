# Genkgo.Push
Send push messages to Android, Apple and Windows using one interface 

### Installation

Requires PHP 5.6 or later. There are no plans to support PHP 5.5 or earlier. PRs in this matter are rejected. It is installable and autoloadable via Composer as [genkgo/push](https://packagist.org/packages/genkgo/push).

### Quality

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/genkgo/push/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/genkgo/push/)
[![Code Coverage](https://scrutinizer-ci.com/g/genkgo/push/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/genkgo/push/)
[![Build Status](https://travis-ci.org/genkgo/push.png?branch=master)](https://travis-ci.org/genkgo/push)

To run the unit tests at the command line, issue `phpunit -c tests/`. [PHPUnit](http://phpunit.de/manual/) is required.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

## Getting Started


```php
<?php
use Genkgo\Push\Gateway;
use Genkgo\Push\Sender\GoogleGcmSender;
use Genkgo\Push\Sender\AppleApnSender;
use Genkgo\Push\Sender\WindowsSender;
use PHP_GCM\Sender;
use Apple\ApnPush\Certificate\Certificate;
use Apple\ApnPush\Notification\Connection;
use JildertMiedema\WindowsPhone\WindowsPhonePushNotification;

// construct the gateway, using the different senders (using other composer packages)
$gateway = new Gateway([
    new GoogleGcmSender(new Sender('API key obtained through the Google API Console'),
    new AppleApnSender(new Connection(new Certificate('/location/to/cert.pem', 'passphrase'))),
    new WindowsSender(new WindowsPhonePushNotification())
]);

// below message will automatically go to their own specific sender
$gateway->send(new Message(new Body('message content')), new AndroidDeviceRecipient('token'));
$gateway->send(new Message(new Body('message content')), new AppleDeviceRecipient('token'));
$gateway->send(new Message(new Body('message content')), new WindowsDeviceRecipient('token'));
```

## Contributing

- Found a bug? Please try to solve it yourself first and issue a pull request. If you are not able to fix it, at least
  give a clear description what goes wrong. We will have a look when there is time.
- Want to see a feature added, issue a pull request and see what happens. You could also file a bug of the missing
  feature and we can discuss how to implement it.
