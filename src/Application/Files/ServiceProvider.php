<?php

namespace DropParty\Application\Files;

use DropParty\Domain\Files\FileAccessLogRepository;
use DropParty\Domain\Files\FileHashIdRepository;
use DropParty\Domain\Files\FileRepository;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        FileRepository::class,
        FileHashIdRepository::class,
        FileAccessLogRepository::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->share(FileRepository::class, function () {
            return $this->container->get(DbalFileRepository::class);
        });

        $this->container->share(FileHashIdRepository::class, function () {
            return $this->container->get(DbalFileHashIdRepository::class);
        });

        $this->container->share(FileAccessLogRepository::class, function () {
            return $this->container->get(DbalFileAccessLogRepository::class);
        });
    }
}
