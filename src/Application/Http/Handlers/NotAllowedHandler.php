<?php

namespace DropParty\Application\Http\Handlers;

use DropParty\Infrastructure\Http\Handlers\AbstractViewHandler;
use Slim\Http\Request;
use Slim\Http\Response;
use Teapot\StatusCode;

class NotAllowedHandler extends AbstractViewHandler
{
    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return '405.html.twig';
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->render($request, $response->withStatus(StatusCode::METHOD_NOT_ALLOWED));
    }
}
