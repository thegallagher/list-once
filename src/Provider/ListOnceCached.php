<?php

namespace ListOnce\Provider;

use GuzzleHttp\Client;
use Psr\Cache\CacheItemPoolInterface;

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
    protected $cacheItemPool = null;

    /**
     * Constructor
     *
     * @param string $apiKey
     * @param Client $httpClient
     * @param CacheItemPoolInterface $cacheItemPool
     */
    public function __construct($apiKey, Client $httpClient, CacheItemPoolInterface $cacheItemPool)
    {
        parent::__construct($apiKey, $httpClient);
        $this->cacheItemPool = $cacheItemPool;
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