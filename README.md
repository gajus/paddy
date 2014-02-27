# Skip

[![Build Status](https://travis-ci.org/gajus/skip.png?branch=master)](https://travis-ci.org/gajus/skip)
[![Coverage Status](https://coveralls.io/repos/gajus/skip/badge.png)](https://coveralls.io/r/gajus/skip)

> You can't be a skipper without a great, big vessel and a matey parrot. No nay ne'er!

Utility to persist data (messages) between requests, generate URLs and handle redirects.

## URLs

Ship instance carries predefined routes that are used to construct URLs.

```php
// Set default route:
$ship = new \Gajus\Skip\Ship('http://gajus.com/');
// Set "static" route:
$ship->setRoute('http://static.gajus.com/', 'static');

// Get default route:
// http://gajus.com/
$ship->url();

// Get static route:
// http://static.gajus.com/
$this->url(null, 'static');

// Get absolute URL for the default route:
// http://gajus.com/post/1
$ship->url('post/1');

// Get absolute URL for the "static" route:
// http://static.gajus.com/css/frontend.css
$ship->url('css/frontend.css', 'static');
```

### Redirect

```php
// Redirect to $_SERVER['HTTP_REFERER'] or default to $ship->url():
$ship->go();

// Redirect to the default path with status code 307:
$ship->go( $ship->url(), 307 );
```

> Redirect status code will default to 303 when current request is POST. 302 otherwise.


`go` will throw `Exception\LogicException` exception if [headers have been already sent](http://stackoverflow.com/questions/8028957/how-to-fix-headers-already-sent-error-in-php).

## Bird

> Arr, don't go sailin without your parrot.

Temporarily stores messages in session, then messages can be printed in the next request.

```php
$bird = new \Gajus\Skip\Bird();

$bird->send('Loaded to the Gunwales!');

$ship->go('second');
```

Bird's messages are not removed if page does not produce output:

```php
// Second page
$ship->go('third');
```

Bird's messages are removed from session upon response with output:

```php
// Third page
if ($bird->has('error')) {
    var_dump($bird->getMessages());
}
```

```php
// Fourth page

// Bird no longer carries messages about the original error.
$bird->has('error');
```

### Sending

Message is sent using `send` method. The second parameter is used to put message under a namespace. Default namespace is "error".

```php
$bird->send('There is more grog on the deck!', 'success');
```

Namespace values are limited to "error", "notice" and "success". Limit is imposed to avoid accidental (hard to catch!) typos. If you would like to change this behaviour, [raise an issue](https://github.com/gajus/skip/issues) providing a use case for an alternative/variable namespace.

### Checking for presence, getting and displaying

To check for any message presence under a specific namespace, use method `has`:

```php
$bird->has('error');
```

`getMessages` method returs all of the messages nested under their namespace.

However, you can use the default template to display messages:

```php
$bird->send('a');
$bird->send('b', 'success');

echo $bird->template();
```

```html
<ul class="skip-bird with-messages">
    <li class="error">a</li>
    <li class="success">b</li>
</ul>
```

When there are no messages, `template` will produce:

```html
<ul class="skip-bird no-messages"></ul>
```

## Logging

Both classes implement [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) `LoggerAwareInterface`.
