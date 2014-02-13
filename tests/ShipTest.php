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
     * @expectedExceptionMessage Path is not relative to the route URL.
     */
    public function testGetURLUsingAbsoluteCustomPath () {
        $ship = new \Gajus\Skip\Ship('http://gajus.com/');

        $ship->url('/foo');
    }
}