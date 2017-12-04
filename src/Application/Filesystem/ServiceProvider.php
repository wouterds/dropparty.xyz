<?php

namespace DropParty\Application\Filesystem;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Filesystem;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Filesystem::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->share(Filesystem::class, function () {
            return new Filesystem(new LocalAdapter(APP_DIR . getenv('FILESYSTEM_DIR')));
        });
    }
}
