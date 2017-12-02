<?php

namespace DropParty\Application\Http;

use DropParty\Application\Http\Handlers\ExceptionHandler;
use DropParty\Application\Http\Handlers\NotAllowedHandler;
use DropParty\Application\Http\Handlers\NotFoundHandler;
use Jenssegers\Lean\SlimServiceProvider;

class ServiceProvider extends SlimServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        parent::register();

        $this->container->share('errorHandler', function () {
            return $this->container->get(ExceptionHandler::class);
        });

        $this->container->share('phpErrorHandler', function () {
            return $this->container->get(ExceptionHandler::class);
        });

        $this->container->share('notFoundHandler', function () {
            return $this->container->get(NotFoundHandler::class);
        });

        $this->container->share('notAllowedHandler', function () {
            return $this->container->get(NotAllowedHandler::class);
        });
    }
}
