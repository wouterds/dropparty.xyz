<?php

namespace DropParty\Application\Http;

use DropParty\Application\Container;
use Slim\App;

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

        require __DIR__ . '/routes-api.php';
        require __DIR__ . '/routes-web.php';
    }
}
