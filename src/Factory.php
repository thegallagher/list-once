<?php

namespace ListOnce;

use Http\Client\Socket\Client as Client;
use Http\Discovery\MessageFactoryDiscovery;
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
        $httpClient = new Client(MessageFactoryDiscovery::find());
        if ($cacheItemPool) {
            throw new \RuntimeException('Not implemented');
            //return new ListOnceCached($apiKey, $httpClient, $cacheItemPool);
        }
        return new ListOnce($apiKey, $httpClient);
    }
}