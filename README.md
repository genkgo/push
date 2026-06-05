# Genkgo.Push
Send push messages to Apple, Firebase and Web Push using one interface.

### Installation

Requires a [PHP version getting security updates](https://www.php.net/supported-versions.php). It is installable and autoloadable via Composer as [genkgo/push](https://packagist.org/packages/genkgo/push).

### Quality

![workflow code check](https://github.com/genkgo/push/workflows/code%20check/badge.svg)

To run the unit tests at the command line, issue `phpunit -c tests/`. [PHPUnit](http://phpunit.de/manual/) is required.

This library attempts to comply with [PSR-1][], [PSR-2][], and [PSR-4][]. If
you notice compliance oversights, please send a patch via pull request.

[PSR-1]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md
[PSR-2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md
[PSR-4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md

## Send Push Messages


```php
<?php
use Genkgo\Push\Body;
use Genkgo\Push\Firebase\CloudMessaging;
use Genkgo\Push\Firebase\OauthBearerTokenProvider;
use Genkgo\Push\Gateway;
use Genkgo\Push\Message;
use Genkgo\Push\Sender\FirebaseSender;
use Genkgo\Push\Sender\AppleApnSender;
use Genkgo\Push\Sender\WebPushSender;
use Genkgo\Push\Recipient\AndroidDeviceRecipient;
use Genkgo\Push\Recipient\AppleDeviceRecipient;
use Genkgo\Push\Recipient\FirebaseRecipient;
use Genkgo\Push\Recipient\WebRecipient;
use Genkgo\Push\WebPush\PayloadEncryption;

// firebase oauth token
$auth = new OauthBearerTokenProvider($guzzleClient, $guzzleHttpFactory, '/link/to/services-account.json');

// construct the gateway, using the different senders
$gateway = new Gateway([
    AppleApnSender::fromToken('/location/to/key.p8', 'keyId', 'teamId', 'bundleId'),
    new FirebaseSender(new CloudMessaging($guzzleClient, $guzzleHttpFactory, $auth), 'fcm-project-id'),
    new WebPushSender($guzzleClient, $guzzleHttpFactory, new PayloadEncryption(), '/link/to/private-key.pem', '/link/to/public-key.pem', 'mailto:address@domain.com'),
]);

// below message will automatically go to their own specific sender
$gateway->send(new Message(new Body('message content')), new AppleDeviceRecipient('token'));
$gateway->send(new Message(new Body('message content')), new FirebaseRecipient('token'));
$gateway->send(new Message(new Body('message content')), new WebRecipient('https://endpoint', 'keys.auth', 'keys.p256dh'));
```

## Contributing

- Found a bug? Please try to solve it yourself first and issue a pull request. If you are not able to fix it, at least
  give a clear description what goes wrong. We will have a look when there is time.
- Want to see a feature added, issue a pull request and see what happens. You could also file a bug of the missing
  feature and we can discuss how to implement it.
