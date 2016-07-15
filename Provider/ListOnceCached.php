<?php

namespace ListOnce\Provider;

use Ivory\HttpAdapter\AbstractHttpAdapter;
use Desarrolla2\Cache\Adapter\AbstractAdapter;

/**
 * Class ListOnceCached
 *
 * Uses Wordpress Transient API to cache results
 *
 * @package ListOnce\Provider
 */
class ListOnceCached extends ListOnce
{

    /**
     * Cache pool for storing results
     *
     * @var int
     */
    protected $cacheAdapter = null;

    /**
     * Constructor
     *
     * @param AbstractHttpAdapter $httpAdapter
     * @param AbstractAdapter $cacheAdapter
     * @param string $apiKey
     */
    public function __construct(AbstractHttpAdapter $httpAdapter, AbstractAdapter $cacheAdapter, $apiKey)
    {
        $this->cacheAdapter = $cacheAdapter;
        parent::__construct($httpAdapter, $apiKey);
    }

    /**
     * Query the API
     *
     * Check to see if the query is cached first
     *
     * @param string $function
     * @param array $query
     *
     * @return object
     */
    public function executeQuery($function, $query = [])
    {
        $url = $this->buildQuery($function, $query);
        $result = $this->cacheAdapter->get($url);
        if (!$result) {
            $response = $this->httpAdapter->get($url);
            $result = $this->parseResponse($response);
            $this->cacheAdapter->set($url, $result);
        }
        return $result;
    }
}