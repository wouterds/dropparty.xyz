<?php

namespace DropParty\Application\Http\Handlers;

use Slim\Http\Request;
use Slim\Http\Response;

class SignOutHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        // Delete cookie
        setcookie('uid', null, -1);

        return $response->withRedirect('/');
    }
}
