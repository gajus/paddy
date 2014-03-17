<?php
class ShipTest extends PHPUnit_Framework_TestCase {
    public function testSetDefaultRoute () {
        $ship = new \Gajus\Skip\Ship('http://gajus.com/');

        $this->assertSame('http://gajus.com/', $ship->getRoute('default'));
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid URL.
     */
    public function testSetRouteInvalidURL () {
        $ship = new \Gajus\Skip\Ship('foo');
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage URL does not refer to a directory.
     */
    public function testSetRouteURLNotBase () {
        $ship = new \Gajus\Skip\Ship('http://gajus.com');
    }

    public function testSetCustomRoute () {
        $ship = new \Gajus\Skip\Ship('http://gajus.com/');

        $ship->setRoute('foo', 'http://gajus.com/foo/');

        $this->assertSame('http://gajus.com/foo/', $ship->getRoute('foo'));
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage Cannot overwrite existing route.
     */
    public function testOverwriteExistingRoute () {
        $ship = new \Gajus\Skip\Ship('http://gajus.com/');

        $ship->setRoute('foo', 'http://gajus.com/foo/');
        $ship->setRoute('foo', 'http://gajus.com/foo/');
    }

    public function testGetURLDefaultRoute () {
        $ship = new \Gajus\Skip\Ship('http://gajus.com/');

        $this->assertSame('http://gajus.com/', $ship->url());
    }

    public function testGetURLUsingCustomPath () {
        $ship = new \Gajus\Skip\Ship('http://gajus.com/');

        $this->assertSame('http://gajus.com/foo', $ship->url('foo'));
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage Route does not exist.
     */
    public function testGetURLNotExistingRoute () {
        $ship = new \Gajus\Skip\Ship('http://gajus.com/');

        $ship->url('foo', 'foobar');
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage Path is not relative to the route.
     */
    public function testGetURLUsingAbsoluteCustomPath () {
        $ship = new \Gajus\Skip\Ship('http://gajus.com/');

        $ship->url('/foo');
    }

    public function testGetPath () {
        $ship = new \Gajus\Skip\Ship('https://gajus.com/foo/');

        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'gajus.com';
        $_SERVER['REQUEST_URI'] = '/foo/';

        $this->assertSame('', $ship->getPath());

        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'gajus.com';
        $_SERVER['REQUEST_URI'] = '/foo/bar/';

        $this->assertSame('bar/', $ship->getPath());

        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'gajus.com';
        $_SERVER['REQUEST_URI'] = '/foo/bar/?foo[bar]=1';

        $this->assertSame('bar/', $ship->getPath());
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage Route is using a different scheme.
     */
    public function testGetPathDifferentScheme () {
        $ship = new \Gajus\Skip\Ship('http://gajus.com/foo/');

        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'gajus.com';
        $_SERVER['REQUEST_URI'] = '/foo/';

        $ship->getPath();
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage Route has a different host.
     */
    public function testGetPathDifferentHost () {
        $ship = new \Gajus\Skip\Ship('https://gajus.com/foo/');

        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'gajus.io';
        $_SERVER['REQUEST_URI'] = '/foo/';

        $ship->getPath();
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage Request URI does not extend the route.
     */
    public function testGetPathRouteURINotUnderTheRoute () {
        $ship = new \Gajus\Skip\Ship('https://gajus.com/foo/bar/');

        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'gajus.com';
        $_SERVER['REQUEST_URI'] = '/';

        $ship->getPath();

        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_NAME'] = 'gajus.com';
        $_SERVER['REQUEST_URI'] = '/foo/';

        $ship->getPath();
    }
}