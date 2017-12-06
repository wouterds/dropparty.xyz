<?php

namespace DropParty\Infrastructure\Cache;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Predis\Client as RedisClient;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Cache::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->container->share(Cache::class, function () {
            return new Cache(new RedisClient([
                'scheme' => 'tcp',
                'host'   => getenv('REDIS_HOST'),
                'port'   => getenv('REDIS_PORT'),
            ]));
        });
    }
}
