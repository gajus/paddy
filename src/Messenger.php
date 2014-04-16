<?php
namespace Gajus\Paddy;

/**
 * Messenger is a "flash" container used to carry messages between page requests using sessions.
 *
 * @link https://github.com/gajus/paddy for the canonical source repository
 * @license https://github.com/gajus/paddy/blob/master/LICENSE BSD 3-Clause
 */
class Messenger implements \Psr\Log\LoggerAwareInterface {
    private
        /**
         * @var Psr\Log\LoggerInterface
         */
        $logger,
        /**
         * @var string $namespace
         */
        $namespace,
        /**
         * @var array $messages
         */
        $messages = [];

    /**
     * @param string $namespace Namespace is used if more than one application is using Messenger. Defaults to the SERVER_NAME or "default".
     */
    public function __construct ($namespace = null) {
        $this->logger = new \Psr\Log\NullLogger;

        if (session_status() === PHP_SESSION_NONE) {
            throw new Exception\LogicException('Session must be started before using Bird.');
        }

        if ($namespace === null) {
            $namespace = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'default';
        }

        $this->namespace = $namespace;
        $this->messages = isset($_SESSION['gajus']['paddy']['messenger'][$this->getNamespace()]) ? $_SESSION['gajus']['paddy']['messenger'][$this->getNamespace()] : [];

        // Otherwise PHP will pick up Gajus\Paddy\Messenger::Gajus\Paddy\{closure}.
        $method_name = __METHOD__;

        /**
         * Messages are stored if there is no content displayed.
         * Messages are discarded if there is content displayed.
         *
         * Do not use __destruct for this, because object can be destructed before the end of the script.
         * 
         * @see http://stackoverflow.com/questions/21737903/how-to-get-content-length-at-the-end-of-request#21737991 Detect if body has been sent to the browser.
         * @codeCoverageIgnore
         */
        register_shutdown_function(function () use ($method_name) {
            if (count(array_filter(ob_get_status(true), function ($status) { return $status['buffer_used']; } ))) {
                $this->logger->debug('Output buffer. Discarding messages.', ['method' => $method_name]);
                
                $_SESSION['gajus']['paddy']['messenger'][$this->getNamespace()] = [];
            } else {
                $this->logger->debug('No output buffer. Storring messages.', ['method' => $method_name]);
    
                $_SESSION['gajus']['paddy']['messenger'][$this->getNamespace()] = $this->messages;
            }
        });
    }

    /**
     * @return string
     */
    public function getNamespace () {
        return $this->namespace;
    }

    /**
     * @param string $message
     * @param string $namespace
     * @return $this
     */
    public function send ($message, $namespace = 'error') {
        $this->logger->debug('Sending message.', ['method' => __METHOD__, 'message' => $message, 'namespace' => $namespace]);
        
        if (!is_string($message)) {
            throw new Exception\InvalidArgumentException('Message is not a string.');
        }

        // Limit namespace to [success, error, notice] (to avoid typos).
        if (!in_array($namespace, ['success', 'error', 'notice'], true)) {
            throw new Exception\InvalidArgumentException('Invalid message namespace.');
        }

        $this->messages[$namespace][] = $message;

        return $this;
    }

    /**
     * Return all messages.
     * 
     * @return array
     */
    public function getMessages () {
        return $this->messages;
    }

    /**
     * Return messages in a container.
     *
     * @return string
     */
    public function getNest () {
        $messages = $this->getMessages();
        $messages_body = '';

        if ($messages) {
            $container_name = 'paddy-messenger-nest with-messages';

            foreach ($messages as $namespace => $submessages) {
                foreach ($submessages as $message) {
                    $messages_body .= '<li class="' . $namespace . '">' . $message . '</li>';
                }
            }
        } else {
            $container_name = 'paddy-messenger-nest no-messages';
        }

        return '<ul class="' . $container_name . '">' . $messages_body . '</ul>';
    }

    /**
     * Check if there are errors in a particular namespace.
     * 
     * @param string $namespace
     * @return boolean
     */
    public function has ($namespace) {
        return isset($this->messages[$namespace]);
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     * @codeCoverageIgnore
     */
    public function setLogger (\Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;
    }
}