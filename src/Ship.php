<?php
namespace Gajus\Skip;

/**
 * @link https://github.com/gajus/skip for the canonical source repository
 * @license https://github.com/gajus/skip/blob/master/LICENSE BSD 3-Clause
 */
class Ship implements \Psr\Log\LoggerAwareInterface {
    private
        /**
         * @var Psr\Log\LoggerInterface
         */
        $logger,
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
        if ($this->logger) {
            $this->logger->debug('Set route.', ['method' => __METHOD__, 'name' => $name, 'url' => $url]);
        }

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
     * Redirect user agent to provided URL.
     * If no $url provided, redirect to the referrer or
     * (when referre is not available) to the default path.
     * 
     * @see http://benramsey.com/blog/2008/07/http-status-redirection/
     * @param string|null $url Absolute URL
     * @return void
     * @codeCoverageIgnore
     */
    public function go ($url = null, $response_code = null) {
        if ($this->logger) {
            $this->logger->debug('Go.', ['method' => __METHOD__, 'url' => $url, 'response_code' => $response_code]);
        }

        if (headers_sent()) {
            throw new Exception\LogicException('Headers have been already sent.');
        }

        if (is_null($response_code)) {
            $response_code = $_SERVER['REQUEST_METHOD'] === 'POST' ? '303' : '302';
        }

        if (is_null($url)) {
            $url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->url();
        }

        \http_response_code($response_code);

        header('Location: ' . $url);

        exit;
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