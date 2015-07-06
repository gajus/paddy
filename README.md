# Paddy

[![Build Status](https://img.shields.io/travis/gajus/paddy.svg?style=flat)](https://travis-ci.org/gajus/paddy)
[![Coverage Status](https://img.shields.io/coveralls/jekyll/jekyll.svg?style=flat)](https://coveralls.io/r/gajus/paddy?branch=master)
[![Latest Stable Version](https://img.shields.io/packagist/v/gajus/paddy.svg?style=flat)](https://packagist.org/packages/gajus/paddy)

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

## Sending a Message

Message is sent using `send` method. The second parameter is used to put message under a namespace.

```php
$messenger->send('There is more grog on the deck!', 'success');
```

Namespace values are limited to "error", "notice" and "success". Limit is imposed to avoid accidental (and hard to catch) typos. If you would like to change this behaviour, [raise an issue](https://github.com/gajus/skip/issues) including an example use case for an alternative or a variable namespace.

## Getting Messages

To get all messages nested under the respective namespace, use `getMessages` method:

```php
/**
 * Return all messages nested under the respective message namespace.
 * 
 * @return array
 */
$messenger->getMessages();
```

To check if there are messages under a specific namespace, use `has` method:

```php
/**
 * Check if there are messages under the specified message namespace.
 * 
 * @param string $namespace
 * @return boolean
 */
$messenger->has('error');
```

### Message Holder

Use the message holder when you intend to display messages to the end user:

```php
$messenger->send('a');
$messenger->send('b', 'success');

echo $messenger->getMessageHolder();
```

```html
<ul class="paddy-messenger with-messages">
    <li class="error">a</li>
    <li class="success">b</li>
</ul>
```

When there are no messages, `getMessageHolder` will produce:

```html
<ul class="paddy-messenger no-messages"></ul>
```

The empty tag is used for interoperability with the frontend script.

Proposed stylesheet:

```scss
.paddy-messenger {
    display: none;

    li {
        display: block; padding: 20px; color: #fff;

        &.error {
            background: #E74C3C;
        }

        &.notice {
            background: #F1C40F;
        }

        &.important {
            background: #3498DB;
        }

        &.success {
            background: #27AE60;
        }
    }

    &.with-messages {
        display: block;
    }
}
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

```php
// Page 1
$messenger->error('foo');
```

Response that has "location" header continue to persist message data:

```php
// Page 2
header('Location: Page 3');
```

Messages are discarded after the first page is displayed:

```php
// Page 3
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
