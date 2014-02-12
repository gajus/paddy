<?php
namespace Gajus\Skip;

/**
 * Bucket is a "flash" container used to carry data between page requests using sessions.
 * Data assigned to the container is destroyed either implicitly
 *
 * @link https://github.com/gajus/skip for the canonical source repository
 * @license https://github.com/gajus/skip/blob/master/LICENSE BSD 3-Clause
 */
class Bucket implements \ArrayAccess {
    private
        /**
         * @var string $uid Unique ID for the session container.
         */
        $uid,
        /**
         * @var array $data
         */
        $data = [];

    public function __construct ($uid) {
        if (session_status() == PHP_SESSION_NONE) {
            throw new Exception\LogicException('Session must be started before using Bucket.');
        }

        $this->uid = $uid;

        $this->data = isset($_SESSION['gajus']['skip']['bucket'][$uid]) ? $_SESSION['gajus']['skip']['bucket'][$uid] : [];
    }

    public function getUid () {
        return $this->uid;
    }

    public function offsetExists ($offset) {
        return isset($this->data[$offset]);
    }

    public function offsetGet ($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : [];
    }

    public function offsetSet ($offset, $value) {
        $this->data[$offset] = $value;
    }

    public function offsetUnset ($offset) {
        unset($this->data[$offset]);
    }

    /**
     * @see http://stackoverflow.com/questions/21737903/how-to-get-content-length-at-the-end-of-request#21737991 Detect if body has been sent to the browser.
     */
    public function __destruct () {
        register_shutdown_function(function () {
            if (count(array_filter(ob_get_status(true), function ($status) { return $status['buffer_used']; } ))) {
                $_SESSION['gajus']['skip']['bucket'][$this->getUid()] = [];
            }
        });
    }
}