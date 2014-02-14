<?php
class BirdTest extends PHPUnit_Framework_TestCase {
    public function setUp () {
        @session_start();
    }

    /**
     * @expectedException Gajus\Skip\Exception\LogicException
     * @expectedExceptionMessage Session must be started before using Bird.
     */
    public function testInitialiseWithoutSession () {
        session_destroy();

        new \Gajus\Skip\Bird();
    }

    public function testGetDefaultBirdName () {
        $bird = new \Gajus\Skip\Bird();

        $this->assertSame('default', $bird->getName());
    }

    public function testSetBirdName () {
        $bird = new \Gajus\Skip\Bird('john');

        $this->assertSame('john', $bird->getName());
    }

    public function testHasMessage () {
        $_SESSION['gajus']['skip']['bird']['john'] = ['error' => ['test']];

        $bird = new \Gajus\Skip\Bird('john');

        $this->assertTrue($bird->has('error'));
    }

    public function testGetMessages () {
        $_SESSION['gajus']['skip']['bird']['john'] = ['error' => ['test']];

        $bird = new \Gajus\Skip\Bird('john');

        $this->assertSame(['error' => ['test']], $bird->getMessages());
    }

    public function testSendMessageDefaultNamespace () {
        $bird = new \Gajus\Skip\Bird();

        $bird->send('test');

        $this->assertSame(['error' => ['test']], $bird->getMessages());
    }

    public function testSendMessageCustomNamespace () {
        $bird = new \Gajus\Skip\Bird();

        $bird->send('test', 'success');

        $this->assertSame(['success' => ['test']], $bird->getMessages());
    }

    /**
     * @expectedException Gajus\Skip\Exception\UnexpectedValueException
     * @expectedExceptionMessage Message is not a string.
     */
    public function testSendMessageNotString () {
        $bird = new \Gajus\Skip\Bird();

        $bird->send(['test']);
    }

    public function testSendMessageReturnBird () {
        $bird = new \Gajus\Skip\Bird();

        $this->assertSame($bird, $bird->send('test'));
    }

    public function testEmptyTemplate () {
        $bird = new \Gajus\Skip\Bird();

        $this->assertSame('<ul class="skip-bird no-messages"></ul>', $bird->template() );
    }

    public function testTemplate () {
        $bird = new \Gajus\Skip\Bird();

        $bird->send('a');
        $bird->send('b', 'success');

        $this->assertSame('<ul class="skip-bird with-messages"><li>a</li><li>b</li></ul>', $bird->template() );
    }
}