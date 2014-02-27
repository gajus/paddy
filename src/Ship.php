<?php
namespace Gajus\Skip;

/**
 * Skip is a utility for generating URLs relative to predefined routes.
 * Inject an instance of Ship to your template and use it to generate links.
 * Then if your URLs schema ever changes, you will be able to adjust it in the
 * Ship configuration, subject to that resource path does not change.
 * 
 * @link https://github.com/gajus/skip for the canonical source repository
 * @license https://github.com/gajus/skip/blob/master/LICENSE BSD 3-Clause
 */
class Ship implements \Psr\Log\LoggerAwareInterface {
    private
        /**
         * @var Psr\Log\LoggerInterface
         */
        $logger,
        /**
         * @array $routes
         */
        $routes = [];

    /**
     * @param string $url Default route URL.
     */
    public function __construct ($url) {
        $this->setRoute('default', $url);
    }

    /**
     * 
     * @param string $name Route name.
     * @param string $url URL.
     * @todo Check if query string is included.
     */
    public function setRoute ($name, $url) {
        if ($this->logger) {
            $this->logger->debug('Set route.', ['method' => __METHOD__, 'name' => $name, 'url' => $url]);
        }

        if (isset($this->routes[$name])) {
            throw new Exception\InvalidArgumentException('Cannot overwrite existing route.');
        } else if (!filter_var($url, \FILTER_VALIDATE_URL)) {
            throw new Exception\InvalidArgumentException('Invalid URL.');
        } else if (mb_strpos(strrev($url), '/') !== 0) {
            throw new Exception\InvalidArgumentException('URL does not refer to a directory.');
        }

        $this->routes[$name] = $url;
    }

    /**
     * @param string $route_name
     * @return string Route base URL.
     */
    public function getRoute ($route_name) {
        if (!isset($this->routes[$route_name])) {
            throw new Exception\InvalidArgumentException('Route does not exist.');
        }

        return $this->routes[$route_name];
    }

    /**
     * This is the inverse of the "url" method. It is used to get the resource path
     * of the current request URI relative to a specific route.
     * 
     * @param string $route_name
     * @param string $request_uri Absolute request URI, part of which is the base URL path.
     * @return string Resource path relative to the route.
     */
    public function getPath ($route_name = 'default') {
        $base_url = $this->getRoute($route_name);

        $base_url = parse_url($base_url);

        if ($base_url['scheme'] !== (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')) {
            throw new Exception\InvalidArgumentException('Route is using a different scheme.');
        }

        if ($base_url['host'] !== $_SERVER['HTTP_HOST']) {
            throw new Exception\InvalidArgumentException('Route has a different host.');
        }

        $request_path = parse_url($_SERVER['REQUEST_URI'], \PHP_URL_PATH);

        $base_path = $base_url['path'];
        $request_path = $request_path;

        #var_dump( $base_path, $request_path ); exit;

        if (mb_strpos($request_path, $base_path) !== 0) {
            throw new Exception\InvalidArgumentException('Request URI does not extend the route.');
        }

        $resource_path = mb_substr($request_path, mb_strlen($base_path));

        return $resource_path;
    }

    /**
     * Get absolute URL using either of the predefined routes.
     * Requested resource path is appended to the route.
     *
     * @param string $path Relavite path to the route.
     * @param string $route Route name.
     */
    public function url ($path = '', $route = 'default') {
        if (strpos($path, '/') === 0) {
            throw new Exception\InvalidArgumentException('Path is not relative to the route.');
        }

        $route = $this->getRoute($route);

        return $route . ltrim($path, '/');
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