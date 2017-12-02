<?php

namespace DropParty\Infrastructure\Http\Handlers;

use Slim\Http\Request;
use Slim\Http\Response;

interface View
{
    /**
     * @return string
     */
    public function getTemplate(): string;

    /**
     * @param Request $request
     * @param Response $response
     * @param array $data
     * @return Response
     */
    public function render(Request $request, Response $response, array $data = []): Response;
}
