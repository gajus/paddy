<?php
namespace Gajus\Skip;

/**
 * Pigeon is a "flash" container used to carry messages between page requests using sessions.
 *
 * @link https://github.com/gajus/skip for the canonical source repository
 * @license https://github.com/gajus/skip/blob/master/LICENSE BSD 3-Clause
 */
class Pigeon {
    private
        /**
         * @var string $name
         */
        $name,
        /**
         * @var array $messages
         */
        $messages = [];

    /**
     * @param string $name Namespace is used if more than one application is using Pigeon, e.g. frontend and backend interface.
     */
    public function __construct ($name = 'default') {
        if (session_status() == PHP_SESSION_NONE) {
            throw new Exception\LogicException('Session must be started before using Bucket.');
        }

        $this->name = $name;

        $this->messages = isset($_SESSION['gajus']['skip']['pigeon'][$this->getName()]) ? $_SESSION['gajus']['skip']['pigeon'][$this->getName()] : [];
    }

    /**
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    public function send ($message, $namespace = 'error') {
        if (!isset($this->messages[$namespace])) {
            $this->messages[$namespace] = [];
        }

        $this->messages[$namespace][] = $message;
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
     * @param string $namespace
     * @return boolean
     */
    public function has ($namespace) {
        return isset($this->messages[$namespace]);
    }

    /**
     * Pigeo messages are stored if there is no content displayed.
     * Pigeo messages are discarded if there is content displayed.
     * 
     * @see http://stackoverflow.com/questions/21737903/how-to-get-content-length-at-the-end-of-request#21737991 Detect if body has been sent to the browser.
     */
    public function __destruct () {
        register_shutdown_function(function () {
            if (count(array_filter(ob_get_status(true), function ($status) { return $status['buffer_used']; } ))) {
                $_SESSION['gajus']['skip']['pigeon'][$this->getName()] = [];
            } else {
                $_SESSION['gajus']['skip']['pigeon'][$this->getName()] = $this->messages;
            }
        });
    }
}