<?php

namespace ListOnce;

use GuzzleHttp\Client;
use ListOnce\Provider\ListOnce;
use ListOnce\Provider\ListOnceCached;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Class Factory
 *
 * @package ListOnce
 */
class Factory
{

    /**
     * Get an instance of a ListOnce class
     *
     * @param string $apiKey
     * @param CacheItemPoolInterface|null $cacheItemPool
     *
     * @return ListOnce|ListOnceCached
     */
    public static function makeListOnce($apiKey, CacheItemPoolInterface $cacheItemPool = null)
    {
        $httpClient = new Client();
        if ($cacheItemPool) {
            return new ListOnceCached($apiKey, $httpClient, $cacheItemPool);
        }
        return new ListOnce($apiKey, $httpClient);
    }
}