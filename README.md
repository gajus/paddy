# Paddy

[![Build Status](https://travis-ci.org/gajus/paddy.png?branch=master)](https://travis-ci.org/gajus/paddy)
[![Coverage Status](https://coveralls.io/repos/gajus/paddy/badge.png?branch=master)](https://coveralls.io/r/gajus/paddy?branch=master)
[![Latest Stable Version](https://poser.pugx.org/gajus/paddy/version.png)](https://packagist.org/packages/gajus/paddy)
[![License](https://poser.pugx.org/gajus/paddy/license.png)](https://packagist.org/packages/gajus/paddy)

"flash" container used to carry messages between page requests using sessions.

```php
$messenger = new \Gajus\Paddy\Messenger();
$messenger->send('Loaded to the Gunwales!');
```

Messenger's messages are not removed if page does not produce output.


Messenger's messages are removed from session upon response with output:

```php
// Third page
if ($messenger->has('error')) {
    var_dump($messenger->getMessages());
}
```

```php
// Fourth page

// Bird no longer carries messages about the original error.
$messenger->has('error');
```

### Sending

Message is sent using `send` method. The second parameter is used to put message under a namespace. Default namespace is "error".

```php
$messenger->send('There is more grog on the deck!', 'success');
```

Namespace values are limited to "error", "notice" and "success". Limit is imposed to avoid accidental (hard to catch!) typos. If you would like to change this behaviour, [raise an issue](https://github.com/gajus/skip/issues) providing a use case for an alternative/variable namespace.

### Checking for presence, getting and displaying

To check for any message presence under a specific namespace, use method `has`:

```php
$messenger->has('error');
```

`getMessages` method returs all of the messages nested under their namespace.

However, you can use the default template to display messages:

```php
$messenger->send('a');
$messenger->send('b', 'success');

echo $messenger->template();
```

```html
<ul class="paddy-messenger with-messages">
    <li class="error">a</li>
    <li class="success">b</li>
</ul>
```

When there are no messages, `template` will produce:

```html
<ul class="paddy-messenger no-messages"></ul>
```

## Name

Named after pigeon number NPS.43.9451, [Paddy](http://en.wikipedia.org/wiki/Paddy_(pigeon)).

## Logging

Implements [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) `LoggerAwareInterface`.
