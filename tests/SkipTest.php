<?php
class SkipTest extends PHPUnit_Framework_TestCase {
    public function testSetDefaultRoute () {
        $skip = new \Gajus\Ship\Ship('http://gajus.com/');

        $this->assertSame('http://gajus.com/', $skip->getRoute('default'));
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid URL.
     */
    public function testSetRouteInvalidURL () {
        $skip = new \Gajus\Ship\Ship('foo');
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage URL does not refer to a directory.
     */
    public function testSetRouteURLNotBase () {
        $skip = new \Gajus\Ship\Ship('http://gajus.com');
    }

    public function testSetCustomRoute () {
        $skip = new \Gajus\Ship\Ship('http://gajus.com/');

        $skip->setRoute('foo', 'http://gajus.com/foo/');

        $this->assertSame('http://gajus.com/foo/', $skip->getRoute('foo'));
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage Cannot overwrite existing route.
     */
    public function testOverwriteExistingRoute () {
        $skip = new \Gajus\Ship\Ship('http://gajus.com/');

        $skip->setRoute('foo', 'http://gajus.com/foo/');
        $skip->setRoute('foo', 'http://gajus.com/foo/');
    }

    public function testGetURL () {
        $skip = new \Gajus\Ship\Ship('http://gajus.com/');

        $this->assertSame('http://gajus.com/', $skip->url());
    }

    public function testGetURLUsingCustomPath () {
        $skip = new \Gajus\Ship\Ship('http://gajus.com/');

        $this->assertSame('http://gajus.com/foo', $skip->url('foo'));
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage Path is not relative to the route URL.
     */
    public function testGetURLUsingAbsoluteCustomPath () {
        $skip = new \Gajus\Ship\Ship('http://gajus.com/');

        $skip->url('/foo')
    }
}