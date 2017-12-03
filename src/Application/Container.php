<?php

namespace DropParty\Application;

use DropParty\Application\ApiClient\ServiceProvider as ApiClientServiceProvider;
use DropParty\Application\Dropbox\ServiceProvider as DropboxServiceProvider;
use DropParty\Application\Files\ServiceProvider as FilesServiceProvider;
use DropParty\Application\Http\ServiceProvider as HttpServiceProvider;
use DropParty\Application\Oauth\ServiceProvider as OauthServiceProvider;
use DropParty\Application\Users\ServiceProvider as UsersServiceProvider;
use DropParty\Infrastructure\ApplicationMonitor\ServiceProvider as ApplicationMonitorServiceProvider;
use DropParty\Infrastructure\Database\ServiceProvider as DatabaseServiceProvider;
use DropParty\Infrastructure\HashIds\ServiceProvider as HashIdsServiceProvider;
use DropParty\Infrastructure\View\ServiceProvider as ViewServiceProvider;
use League\Container\Container as LeagueContainer;
use League\Container\ReflectionContainer;

class Container extends LeagueContainer
{
    /**
     * @return Container
     */
    public static function load()
    {
        $container = new static();
        $container->delegate(new ReflectionContainer());

        $container->addServiceProvider(ApplicationMonitorServiceProvider::class);
        $container->addServiceProvider(ApiClientServiceProvider::class);
        $container->addServiceProvider(HashIdsServiceProvider::class);
        $container->addServiceProvider(DatabaseServiceProvider::class);
        $container->addServiceProvider(OauthServiceProvider::class);
        $container->addServiceProvider(HttpServiceProvider::class);
        $container->addServiceProvider(ViewServiceProvider::class);
        $container->addServiceProvider(UsersServiceProvider::class);
        $container->addServiceProvider(FilesServiceProvider::class);
        $container->addServiceProvider(DropboxServiceProvider::class);

        return $container;
    }
}
