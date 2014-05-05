<?php
class MessengerTest extends PHPUnit_Framework_TestCase {
    public function setUp () {
        @session_start();
    }

    /**
     * @expectedException Gajus\Paddy\Exception\LogicException
     * @expectedExceptionMessage Session must be started before using Paddy.
     */
    public function testInitialiseWithoutSession () {
        session_destroy();

        new \Gajus\Paddy\Messenger();
    }

    public function testGetDefaultNameNoServername () {
        $messenger = new \Gajus\Paddy\Messenger();

        $this->assertSame('default', $messenger->getNamespace());
    }

    public function testGetDefaultNameWithServername () {
        $_SERVER['SERVER_NAME'] = 'gajus.com';

        $messenger = new \Gajus\Paddy\Messenger();

        $this->assertSame('gajus.com', $messenger->getNamespace());
    }

    public function testSetNamespace () {
        $messenger = new \Gajus\Paddy\Messenger('john');

        $this->assertSame('john', $messenger->getNamespace());
    }

    public function testHasMessage () {
        $_SESSION['gajus']['paddy']['messenger']['john'] = ['error' => ['test']];

        $messenger = new \Gajus\Paddy\Messenger('john');

        $this->assertTrue($messenger->has('error'));
    }

    public function testGetMessages () {
        $_SESSION['gajus']['paddy']['messenger']['john'] = ['error' => ['test']];

        $messenger = new \Gajus\Paddy\Messenger('john');

        $this->assertSame(['error' => ['test']], $messenger->getMessages());
    }

    /**
     * @dataProvider sendMessageExplicitNamespaceProvider
     */
    public function testSendMessageExplicitNamespace ($namespace) {
        $messenger = new \Gajus\Paddy\Messenger();

        $this->assertFalse($messenger->has($namespace));

        $this->assertInstanceOf('Gajus\Paddy\Messenger', $messenger->send('test', $namespace));

        $messages = [];
        $messages[$namespace] = ['test'];

        $this->assertSame($messages, $messenger->getMessages());

        $this->assertTrue($messenger->has($namespace));
    }

    /**
     * @dataProvider sendMessageExplicitNamespaceProvider
     */
    public function testSendMessageExplicitNamespaceShorthand ($namespace) {
        $messenger = new \Gajus\Paddy\Messenger();

        $this->assertFalse($messenger->has($namespace));

        $this->assertInstanceOf('Gajus\Paddy\Messenger', $messenger->{$namespace}('test'));

        $messages = [];
        $messages[$namespace] = ['test'];

        $this->assertSame($messages, $messenger->getMessages());

        $this->assertTrue($messenger->has($namespace));
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
        $messenger = new \Gajus\Paddy\Messenger();

        $messenger->send('test', 'foo');
    }

    /**
     * @expectedException Gajus\Paddy\Exception\InvalidArgumentException
     * @expectedExceptionMessage Message is not a string.
     */
    public function testSendMessageNotString () {
        $messenger = new \Gajus\Paddy\Messenger();

        $messenger->send(['test'], 'error');
    }

    public function testSendMessageReturnItself () {
        $messenger = new \Gajus\Paddy\Messenger();

        $this->assertSame($messenger, $messenger->send('test', 'error'));
    }

    public function testEmptyNest () {
        $messenger = new \Gajus\Paddy\Messenger();

        $this->assertSame('<ul class="paddy-messenger no-messages"></ul>', $messenger->getMessageHolder() );
    }

    public function testNest () {
        $messenger = new \Gajus\Paddy\Messenger();

        $messenger->send('a', 'error');
        $messenger->send('b', 'success');

        $this->assertSame('<ul class="paddy-messenger with-messages"><li class="error">a</li><li class="success">b</li></ul>', $messenger->getMessageHolder() );
    }
}