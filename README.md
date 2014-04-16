# Paddy

[![Build Status](https://travis-ci.org/gajus/paddy.png?branch=master)](https://travis-ci.org/gajus/paddy)
[![Coverage Status](https://coveralls.io/repos/gajus/paddy/badge.png?branch=master)](https://coveralls.io/r/gajus/paddy?branch=master)
[![Latest Stable Version](https://poser.pugx.org/gajus/paddy/version.png)](https://packagist.org/packages/gajus/paddy)
[![License](https://poser.pugx.org/gajus/paddy/license.png)](https://packagist.org/packages/gajus/paddy)

Messenger is used to carry (flash) messages between page requests using [session](http://www.php.net/manual/en/features.sessions.php).

```php
/**
 * @param string $namespace Namespace is used if more than one application is using Messenger. Defaults to the SERVER_NAME or "default".
 */
$messenger = new \Gajus\Paddy\Messenger();

/**
 * @param string $message
 * @param string $namespace Message namespace (success, error or notice).
 * @return $this
 */
$messenger->send('Loaded to the Gunwales!');
```

## Sending

Message is sent using `send` method. The second parameter is used to put message under a namespace.

```php
$messenger->send('There is more grog on the deck!', 'success');
```

Namespace values are limited to "error", "notice" and "success". Limit is imposed to avoid accidental (and hard to catch) typos. If you would like to change this behaviour, [raise an issue](https://github.com/gajus/skip/issues) providing a use case for an alternative or variable namespace.

## Getting

To check for any message presence under a specific namespace, use method `has`:

```php
$messenger->has('error');
```

`getMessages` method returs all of the messages under their namespace.

### Templates

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

## Shorthand

```php
/**
 * Shorthand method to send message under "error" namespace.
 *
 * @param string $message
 * @return $this
 */
public function error ($message) {
    return $this->send($message, 'error');
}

/**
 * Shorthand method to send message under "success" namespace.
 *
 * @param string $message
 * @return $this
 */
public function success ($message) {
    return $this->send($message, 'success');
}

/**
 * Shorthand method to send message under "notice" namespace.
 *
 * @param string $message
 * @return $this
 */
public function notice ($message) {
    return $this->notice($message, 'notice');
}
```

## Quasi-Persistency

Messages are carried across pages using `$_SESSION` variable. This requires that you start session before using Paddy.

```php Page 1
$messenger->error('foo');
```

Requests that result in header-only response continue to persist message data:

```php Page 2
header('Location: Page 3');
```

Messages are removed from session upon request resulting in ouput or without `Location:` header.

```php Page 3
var_dump($messenger->has('error'));
```

```
array(1) {
  [0]=>
  bool(true)
}
```

## Name

Named after pigeon number NPS.43.9451, [Paddy](http://en.wikipedia.org/wiki/Paddy_(pigeon)).

## Logging

Implements [PSR-3](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md) `LoggerAwareInterface`.
