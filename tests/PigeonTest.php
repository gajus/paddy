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

    public function testGetDefaultPigeonName () {
        $pigeon = new \Gajus\Skip\Pigeon();

        $this->assertSame('default', $pigeon->getName());
    }

    public function testSetPigeonName () {
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

    /**
     * @expectedException Gajus\Skip\Exception\UnexpectedValueException
     * @expectedExceptionMessage Message is not a string.
     */
    public function testSendMessageNotString () {
        $pigeon = new \Gajus\Skip\Pigeon();

        $pigeon->send(['test']);
    }

    public function testSendMessageReturnPigeon () {
        $pigeon = new \Gajus\Skip\Pigeon();

        $this->assertSame($pigeon, $pigeon->send('test'));
    }

    public function testEmptyTemplate () {
        $pigeon = new \Gajus\Skip\Pigeon();

        $this->assertSame('<ul class="skip-pigeon no-messages"></ul>', $pigeon->template() );
    }

    public function testTemplate () {
        $pigeon = new \Gajus\Skip\Pigeon();

        $pigeon->send('a');
        $pigeon->send('b', 'success');

        $this->assertSame('<ul class="skip-pigeon with-messages"><li>a</li><li>b</li></ul>', $pigeon->template() );
    }
}