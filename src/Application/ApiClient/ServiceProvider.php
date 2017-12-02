<?php

namespace DropParty\Application\ApiClient;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        DropPartyClient::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->share(DropPartyClient::class, function () {
            return new DropPartyClient(
                getenv('APP_API_URL')
            );
        });
    }
}
