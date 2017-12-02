<?php

namespace DropParty\Application\Files;

use DropParty\Domain\Files\FileAccessLogRepository;
use DropParty\Domain\Files\FileHashidRepository;
use DropParty\Domain\Files\FileRepository;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        FileRepository::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->share(FileRepository::class, function () {
            return $this->container->get(DbalFileRepository::class);
        });

        $this->container->share(FileHashidRepository::class, function () {
            return $this->container->get(DbalFileHashidRepository::class);
        });

        $this->container->share(FileAccessLogRepository::class, function () {
            return $this->container->get(DbalFileAccessLogRepository::class);
        });
    }
}
