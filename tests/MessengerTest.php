<?php
class MessengerTest extends PHPUnit_Framework_TestCase {
    public function setUp () {
        @session_start();
    }

    /**
     * @expectedException Gajus\Paddy\Exception\LogicException
     * @expectedExceptionMessage Session must be started before using Bird.
     */
    public function testInitialiseWithoutSession () {
        session_destroy();

        new \Gajus\Paddy\Messenger();
    }

    public function testGetDefaultBirdNameNoServername () {
        $bird = new \Gajus\Paddy\Messenger();

        $this->assertSame('default', $bird->getNamespace());
    }

    public function testGetDefaultBirdNameWithServername () {
        $_SERVER['SERVER_NAME'] = 'gajus.com';

        $bird = new \Gajus\Paddy\Messenger();

        $this->assertSame('gajus.com', $bird->getNamespace());
    }

    public function testSetBirdName () {
        $bird = new \Gajus\Paddy\Messenger('john');

        $this->assertSame('john', $bird->getNamespace());
    }

    public function testHasMessage () {
        $_SESSION['gajus']['paddy']['messenger']['john'] = ['error' => ['test']];

        $bird = new \Gajus\Paddy\Messenger('john');

        $this->assertTrue($bird->has('error'));
    }

    public function testGetMessages () {
        $_SESSION['gajus']['paddy']['messenger']['john'] = ['error' => ['test']];

        $bird = new \Gajus\Paddy\Messenger('john');

        $this->assertSame(['error' => ['test']], $bird->getMessages());
    }

    public function testSendMessageImplicitNamespace () {
        $bird = new \Gajus\Paddy\Messenger();

        $bird->send('test');

        $this->assertSame(['error' => ['test']], $bird->getMessages());
    }

    /**
     * @dataProvider sendMessageExplicitNamespaceProvider
     */
    public function testSendMessageExplicitNamespace ($namespace) {
        $bird = new \Gajus\Paddy\Messenger();

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
     * @expectedException Gajus\Paddy\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid message namespace.
     */
    public function testSendMessageUnexpectedNamespace () {
        $bird = new \Gajus\Paddy\Messenger();

        $bird->send('test', 'foo');
    }

    /**
     * @expectedException Gajus\Paddy\Exception\InvalidArgumentException
     * @expectedExceptionMessage Message is not a string.
     */
    public function testSendMessageNotString () {
        $bird = new \Gajus\Paddy\Messenger();

        $bird->send(['test']);
    }

    public function testSendMessageReturnBird () {
        $bird = new \Gajus\Paddy\Messenger();

        $this->assertSame($bird, $bird->send('test'));
    }

    public function testEmptyNest () {
        $bird = new \Gajus\Paddy\Messenger();

        $this->assertSame('<ul class="paddy-messenger-nest no-messages"></ul>', $bird->getNest() );
    }

    public function testNest () {
        $bird = new \Gajus\Paddy\Messenger();

        $bird->send('a');
        $bird->send('b', 'success');

        $this->assertSame('<ul class="paddy-messenger-nest with-messages"><li class="error">a</li><li class="success">b</li></ul>', $bird->getNest() );
    }
}