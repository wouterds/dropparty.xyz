<?php

namespace DropParty\Infrastructure\ApplicationMonitor;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        ApplicationMonitor::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->share(ApplicationMonitor::class);
    }
}
