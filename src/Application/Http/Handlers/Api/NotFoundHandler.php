<?php

namespace DropParty\Application\Http\Handlers\Api;

use Slim\Http\Request;
use Slim\Http\Response;
use Teapot\StatusCode;

class NotFoundHandler
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        return $response->withStatus(StatusCode::NOT_FOUND);
    }
}
