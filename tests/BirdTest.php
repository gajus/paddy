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

    public function testSendMessageImplicitNamespace () {
        $bird = new \Gajus\Skip\Bird();

        $bird->send('test');

        $this->assertSame(['error' => ['test']], $bird->getMessages());
    }

    /**
     * @dataProvider sendMessageExplicitNamespaceProvider
     */
    public function testSendMessageExplicitNamespace ($namespace) {
        $bird = new \Gajus\Skip\Bird();

        $bird->send('test', $namespace);

        $messages = [];
        $messages[$namespace] = ['test'];

        $this->assertSame($messages, $bird->getMessages());
    }

    public function sendMessageExplicitNamespaceProvider () {
        return [
            ['success'],
            ['error'],
            ['notice']
        ];
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid message namespace.
     */
    public function testSendMessageUnexpectedNamespace () {
        $bird = new \Gajus\Skip\Bird();

        $bird->send('test', 'foo');
    }

    /**
     * @expectedException Gajus\Skip\Exception\InvalidArgumentException
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

    public function testEmptyNest () {
        $bird = new \Gajus\Skip\Bird();

        $this->assertSame('<ul class="skip-bird-nest no-messages"></ul>', $bird->getNest() );
    }

    public function testNest () {
        $bird = new \Gajus\Skip\Bird();

        $bird->send('a');
        $bird->send('b', 'success');

        $this->assertSame('<ul class="skip-bird-nest with-messages"><li class="error">a</li><li class="success">b</li></ul>', $bird->getNest() );
    }
}