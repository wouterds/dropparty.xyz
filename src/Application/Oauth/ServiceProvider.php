<?php

namespace DropParty\Application\Oauth;

use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var array
     */
    protected $provides = [
        DropboxOauthProvider::class,
    ];

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->container->share(DropboxOauthProvider::class, function () {
            return new DropboxOauthProvider([
                'clientId' => getenv('DROPBOX_CLIENT_ID'),
                'clientSecret' => getenv('DROPBOX_CLIENT_SECRET'),
                'redirectUri' => getenv('APP_URL') . '/integrations/dropbox'
            ]);
        });
    }
}
