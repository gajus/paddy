<?php
class PieonTest extends PHPUnit_Framework_TestCase {
    public function setUp () {
        @session_start();
    }

    /**
     * @expectedException Gajus\Skip\Exception\LogicException
     * @expectedExceptionMessage Session must be started before using Bucket.
     */
    public function testInitialiseWithoutSession () {
        session_destroy();

        new \Gajus\Skip\Pigeon();
    }

    public function testGetDefaultName () {
        $pigeon = new \Gajus\Skip\Pigeon();

        $this->assertSame('default', $pigeon->getName());
    }

    public function testSetName () {
        $pigeon = new \Gajus\Skip\Pigeon('john');

        $this->assertSame('john', $pigeon->getName());
    }

    public function testHasMessage () {
        $_SESSION['gajus']['skip']['pigeon']['john'] = ['error' => ['test']];

        $pigeon = new \Gajus\Skip\Pigeon('john');

        $this->assertTrue($pigeon->has('error'));
    }

    public function testGetMessages () {
        $_SESSION['gajus']['skip']['pigeon']['john'] = ['error' => ['test']];

        $pigeon = new \Gajus\Skip\Pigeon('john');

        $this->assertSame(['error' => ['test']], $pigeon->getMessages());
    }

    public function testSendMessageDefaultNamespace () {
        $pigeon = new \Gajus\Skip\Pigeon();

        $pigeon->send('test');

        $this->assertSame(['error' => ['test']], $pigeon->getMessages());
    }

    public function testSendMessageCustomNamespace () {
        $pigeon = new \Gajus\Skip\Pigeon();

        $pigeon->send('test', 'success');

        $this->assertSame(['success' => ['test']], $pigeon->getMessages());
    }
}