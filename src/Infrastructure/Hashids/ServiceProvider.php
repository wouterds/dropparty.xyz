<?php

namespace DropParty\Infrastructure\Hashids;

use Hashids\Hashids;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Hashids::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->share(Hashids::class, function() {
            return new Hashids(getenv('HASHIDS_SALT'), getenv('HASHIDS_MIN_LENGTH'));
        });
    }
}
