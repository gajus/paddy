<?php
namespace Gajus\Skip;

/**
 * @link https://github.com/gajus/skip for the canonical source repository
 * @license https://github.com/gajus/skip/blob/master/LICENSE BSD 3-Clause
 */
class Ship {
    private
        $map = [];

    /**
     * @param string $url Default route URL.
     */
    public function __construct ($url) {
        $this->setRoute('default', $url);
    }

    /**
     * @param string $name Route name.
     * @param string $url URL.
     */
    public function setRoute ($name, $url) {
        if (isset($this->map[$name])) {
            throw new Exception\InvalidArgumentException('Cannot overwrite existing route.');
        } else if (!filter_var($url, \FILTER_VALIDATE_URL)) {
            throw new Exception\InvalidArgumentException('Invalid URL.');
        } else if (mb_strpos(strrev($url), '/') !== 0) {
            throw new Exception\InvalidArgumentException('URL does not refer to a directory.');
        }

        $this->map[$name] = $url;
    }

    /**
     * @param string $name Route name.
     * @return string Route base URL.
     */
    public function getRoute ($name) {
        if (!isset($this->map[$name])) {
            throw new Exception\InvalidArgumentException('Route does not exist.');
        }

        return $this->map[$name];
    }

    /**
     * Get absolute URL using either of the predefined routes and path relative to that route.
     *
     * @param string $path Relavite path to the route.
     * @param string $route Route name.
     */
    public function url ($path = '', $route = 'default') {
        if (strpos($path, '/') === 0) {
            throw new Exception\InvalidArgumentException('Path is not relative to the route URL.');
        }

        $route = $this->getRoute($route);

        return $route . $path;
    }

    /**
     * Redirect user to a URL.
     * Default to redirect to the referrer or to the default path.
     */
    public function go ($url = null) {
        if (headers_sent()) {
            throw new Exception\LogicException('Headers have been already sent.');
        }

        if (is_null($url)) {
            $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->url();
        }

        header('Location: ' . $url);

        exit;
    }
}