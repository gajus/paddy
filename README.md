# Skip

[![Build Status](https://travis-ci.org/gajus/skip.png?branch=master)](https://travis-ci.org/gajus/skip)
[![Coverage Status](https://coveralls.io/repos/gajus/skip/badge.png)](https://coveralls.io/r/gajus/skip)

> You can't be a skipper without a great, big vessel and a matey parrot. No nay ne'er!

Getting all serious now, – a utility to persist data between requests, generate URLs and handle redirects.

## URLs

Separates in-application URL generation logic away from controller and template.

```php
$vessel = new \Gajus\Skip\Vessel('http://gajus.com/'); // set default route
$vessel->setRoute('http://static.gajus.com/', 'static'); // set "static" route

// Get default route:
// http://gajus.com/
$vessel->url();

// Get absolute URL for the default route:
// http://gajus.com/post/1
$vessel->url('post/1');

// Get absolute URL for the "static" route:
// http://static.gajus.com/css/frontend.css
$vessel->url('css/frontend.css', 'static');
```

### Redirect

```php
// Redirect to $_SERVER['HTTP_REFERER'] or default to $vessel->url():
$vessel->go();

// Redirect to the default path:
$vessel->go( $vessel->url() );
```

The above is equivalent to:

```php
header('Location: ' . $vessel->url('post/1'));

exit;
```

However, it will throw `Exception\LogicException` exception if [headers have been already sent](http://stackoverflow.com/questions/8028957/how-to-fix-headers-already-sent-error-in-php).

## Pigeon

> Arr, don't go sailin without your parrot.

Temporarily stores the messages in session, then messages can be printed in the next request.

Pigeon's messages are removed from persistence upon response that results in output.

```php
$pigeon = new \Gajus\Skip\Pigeon();

$pigeon->send('Loaded to the Gunwales!');

$vessel->go('/second');
```

Pigeon's messages are not removed if page does not produce output beyond headers.

```php
// Second page
$vessel->go('/third');
```

```php
// Third page
if ($pigeon->has('error')) {
    var_dump($pigeon->getMessages());
}
```

```php
// Fourth page

// Pigeon no longer carries messages about the original error.
$pigeon->has('error');
```

### Displaying messages

You can either check for message presense or you can use template to catch all messages.

```php
$pigeon->send('a');
$pigeon->send('b', 'success');

echo $pigeon->template();
```

```html
<ul class="skip-pigeon with-messages">
    <li>a</li>
    <li>b</li>
</ul>
```

When there are no messages, `template` will produce:

```html
<ul class="skip-pigeon no-messages"></ul>
```