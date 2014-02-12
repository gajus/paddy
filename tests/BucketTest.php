<?php
class BucketTest extends PHPUnit_Framework_TestCase {
    public function getUid () {
        $bucket = new \Gajus\Skip\Bucket('foo');

        $this->assertSame('foo', $bucket->getUid());
    }

    public function testGetValue () {
        $_SESSION['gajus']['skip']['bucket']['foo'] = ['a' => 'b'];

        $bucket = new \Gajus\Skip\Bucket('foo');

        $this->assertSame('b', $bucket['a']);
    }

    /*public function testGetRecursiveValue () {
        $_SESSION['gajus']['skip']['bucket']['foo'] = ['a' => ['b' => 'c']];

        $bucket = new \Gajus\Skip\Bucket('foo');

        $this->assertSame('c', $bucket['a']['b']);
    }*/

    public function testSetValue () {
        $bucket = new \Gajus\Skip\Bucket('foo');

        $bucket['a'] = 'b';

        $this->assertSame('b', $bucket['a']);
    }

    /*public function testSetRecursiveValue () {
        $bucket = new \Gajus\Skip\Bucket('foo');

        $bucket['a']['b'] = 'c';

        $this->assertSame('c', $bucket['a']['b']);
    }*/

    public function testIssetValue () {
        $bucket = new \Gajus\Skip\Bucket('foo');

        $bucket['a'] = 'b';

        $this->assertTrue(isset($bucket['a']));
    }

    public function testUnsetValue () {
        $bucket = new \Gajus\Skip\Bucket('foo');

        $bucket['a'] = 'b';

        unset($bucket['a']);

        $this->assertFalse(isset($bucket['a']));
    }
}