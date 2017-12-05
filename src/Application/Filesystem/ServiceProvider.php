<?php

namespace DropParty\Application\Filesystem;

use DropParty\Domain\Dropbox\TokenRepository;
use DropParty\Domain\Users\AuthenticatedUser;
use Emgag\Flysystem\Hash\HashPlugin;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Flysystem\Adapter\Local as LocalAdapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        Filesystem::class,
        LocalFilesystem::class,
        DropboxFilesystem::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->share(LocalFilesystem::class, function () {
            $adapter = new LocalAdapter(APP_DIR . getenv('FILESYSTEM_DIR'));

            $filesystem = new Filesystem($adapter);
            $filesystem->addPlugin(new HashPlugin());

            return $filesystem;
        });

        $this->container->share(DropboxFilesystem::class, function () {
            /** @var AuthenticatedUser $authenticatedUser */
            $authenticatedUser = $this->container->get(AuthenticatedUser::class);

            if (!$authenticatedUser->isLoggedIn()) {
                return null;
            }

            /** @var TokenRepository $dropboxTokenRepository */
            $dropboxTokenRepository = $this->container->get(TokenRepository::class);

            $dropboxToken = $dropboxTokenRepository->findActiveTokenForUserId($authenticatedUser->getUser()->getId());

            if (!$dropboxToken) {
                return null;
            }

            $adapter = new DropboxAdapter(new DropboxClient($dropboxToken->getAccessToken()));

            $filesystem = new Filesystem($adapter);
            $filesystem->addPlugin(new HashPlugin());

            return $filesystem;
        });

        $this->container->share(Filesystem::class, $this->container->get(LocalFilesystem::class));
    }
}
