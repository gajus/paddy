# Skip

[![Build Status](https://travis-ci.org/gajus/skip.png?branch=master)](https://travis-ci.org/gajus/skip)
[![Coverage Status](https://coveralls.io/repos/gajus/skip/badge.png)](https://coveralls.io/r/gajus/skip)

> You can't be a skipper without a great, big vessel and a deep, sturdy bucket. No nay ne'er!

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
$vessel->go('post/1');
```

The above is ~equivalent to:

```php
header('Location: ' . $vessel->url('post/1'));

exit;
```

However, it will throw an exception if [headers have been already sent](http://stackoverflow.com/questions/8028957/how-to-fix-headers-already-sent-error-in-php).

## Bucket

> Arr, whether to carry supplies of grog or for the sprogs' first day.

Temporarily stores the messages in session, then messages can be printed in the next request.

Bucket content is removed from persistence when upon response that results in output.

```php
$bucket = new \Gajus\Skip\Bucket('application name');

// First page
$bucket['error'][] = 'Loaded to the Gunwales!';

header('Location: /second');
```

Bucket content is not removed if page does not produce output beyond headers.

```php
// Second page
header('Location: /third');
```

```php
// Third page
if ($bucket['error']) {
    echo $bucket['error'];
}

header('Location: /fourth');
```

```php
// Fourth page

// $bucket['error'] is empty.
```