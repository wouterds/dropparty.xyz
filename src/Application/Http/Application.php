<?php

namespace DropParty\Application\Http;

use DropParty\Application\Container;
use Slim\App;
use Slim\Http\Request;

class Application extends App
{
    public function __construct()
    {
        parent::__construct(Container::load());

        $this->loadRoutes();
    }

    private function loadRoutes()
    {
        $app = $this;
        $request = $app->getContainer()->get(Request::class);

        if (stripos($request->getUri()->getHost(), 'api') !== false) {
            require __DIR__ . '/routes-api.php';
            return;
        }

        require __DIR__ . '/routes-web.php';
    }
}
