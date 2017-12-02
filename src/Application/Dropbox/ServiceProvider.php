<?php

namespace DropParty\Application\Dropbox;

use DropParty\Domain\Dropbox\TokenRepository;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        TokenRepository::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->share(TokenRepository::class, function () {
            return $this->container->get(DbalTokenRepository::class);
        });
    }
}
