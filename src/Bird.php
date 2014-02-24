<?php
namespace Gajus\Skip;

/**
 * Bird is a "flash" container used to carry messages between page requests using sessions.
 *
 * @link https://github.com/gajus/skip for the canonical source repository
 * @license https://github.com/gajus/skip/blob/master/LICENSE BSD 3-Clause
 */
class Bird implements \Psr\Log\LoggerAwareInterface {
    private
        /**
         * @var Psr\Log\LoggerInterface
         */
        $logger,
        /**
         * @var string $name
         */
        $name,
        /**
         * @var array $messages
         */
        $messages = [];

    /**
     * @param string $name Namespace is used if more than one application is using Bird, e.g. frontend and backend interface.
     */
    public function __construct ($name = 'default') {
        if (session_status() == PHP_SESSION_NONE) {
            throw new Exception\LogicException('Session must be started before using Bird.');
        }

        $this->name = $name;

        $this->messages = isset($_SESSION['gajus']['skip']['bird'][$this->getName()]) ? $_SESSION['gajus']['skip']['bird'][$this->getName()] : [];

        /**
         * Messages are stored if there is no content displayed.
         * Messages are discarded if there is content displayed.
         *
         * Do not use __destruct for this, because object can be destructed before the end of the script.
         * 
         * @see http://stackoverflow.com/questions/21737903/how-to-get-content-length-at-the-end-of-request#21737991 Detect if body has been sent to the browser.
         * @codeCoverageIgnore
         */
        register_shutdown_function(function () {
            if (count(array_filter(ob_get_status(true), function ($status) { return $status['buffer_used']; } ))) {
                if ($this->logger) {
                    $this->logger->debug('Discarding messages because there is content displayed.', ['method' => __METHOD__]);
                }

                $_SESSION['gajus']['skip']['bird'][$this->getName()] = [];
            } else {
                if ($this->logger) {
                    $this->logger->debug('Storing messages because there is content displayed.', ['method' => __METHOD__]);
                }

                $_SESSION['gajus']['skip']['bird'][$this->getName()] = $this->messages;
            }
        });
    }

    /**
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    /**
     * @param string $message
     * @param string $namespace
     * @return $this
     */
    public function send ($message, $namespace = 'error') {
        if ($this->logger) {
            $this->logger->debug('Sending message.', ['method' => __METHOD__, 'message' => $message, 'namespace' => $namespace]);
        }

        if (!is_string($message)) {
            throw new Exception\UnexpectedValueException('Message is not a string.');
        }

        if (!isset($this->messages[$namespace])) {
            $this->messages[$namespace] = [];
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
            $container_name = 'skip-bird-nest with-messages';

            foreach ($messages as $namespace => $submessages) {
                foreach ($submessages as $message) {
                    $messages_body .= '<li class="' . $namespace . '">' . $message . '</li>';
                }
            }
        } else {
            $container_name = 'skip-bird-nest no-messages';
        }

        return '<ul class="' . $container_name . '">' . $messages_body . '</ul>';
    }

    /**
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
     */
    public function setLogger (\Psr\Log\LoggerInterface $logger) {
        $this->logger = $logger;
    }
}