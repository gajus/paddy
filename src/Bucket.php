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
         * @var string $uid Unique session container ID.
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

    /**
     * @return string Unique session container ID.
     */
    public function getUid () {
        return $this->uid;
    }

    /**
     * @param mixed $offset
     * @return boolean
     */
    public function offsetExists ($offset) {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet ($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet ($offset, $value) {
        $this->data[$offset] = $value;
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset ($offset) {
        unset($this->data[$offset]);
    }

    /**
     * Bucket data is stored if there is no content displayed.
     * Bucket data is discarded if there is content displayed.
     * 
     * @see http://stackoverflow.com/questions/21737903/how-to-get-content-length-at-the-end-of-request#21737991 Detect if body has been sent to the browser.
     */
    public function __destruct () {
        register_shutdown_function(function () {
            if (count(array_filter(ob_get_status(true), function ($status) { return $status['buffer_used']; } ))) {
                $_SESSION['gajus']['skip']['bucket'][$this->getUid()] = [];
            } else {
                $_SESSION['gajus']['skip']['bucket'][$this->getUid()] = $this->data;
            }
        });
    }
}