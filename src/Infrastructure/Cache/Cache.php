<?php

namespace DropParty\Infrastructure\Cache;

use Cache\Adapter\Predis\PredisCachePool;
use Predis\ClientInterface as Client;

class Cache extends PredisCachePool
{
    /**
     * @param Client $cache
     */
    public function __construct(Client $cache)
    {
        parent::__construct($cache);
    }
}
