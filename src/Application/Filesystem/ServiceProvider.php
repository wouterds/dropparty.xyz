<?php

namespace DropParty\Application\Filesystem;

use DropParty\Domain\Dropbox\TokenRepository;
use DropParty\Domain\Users\AuthenticatedUser;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Filesystem as LeagueFilesystem;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        LeagueFilesystem::class,
        LocalFilesystem::class,
        DropboxFilesystem::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->share(LocalFilesystem::class, function () {
            return LocalFilesystem::filesystem();
        });

        $this->container->share(DropboxFilesystem::class, function () {
            /** @var AuthenticatedUser $authenticatedUser */
            $authenticatedUser = $this->container->get(AuthenticatedUser::class);

            if (!$authenticatedUser->isLoggedIn()) {
                return null;
            }

            /** @var TokenRepository $dropboxTokenRepository */
            $dropboxTokenRepository = $this->container->get(TokenRepository::class);

            $dropboxToken = $dropboxTokenRepository->findActiveTokenForUserId($authenticatedUser->getUserId());

            if (!$dropboxToken) {
                return null;
            }

            return DropboxFilesystem::filesystemForDropboxToken($dropboxToken);
        });
    }
}
