<?php

namespace DropParty\Application\Http\Middlewares;

use Slim\Http\Request;
use Slim\Http\Response;

class FilesAccessLogsMiddleware
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next)
    {


        return $next($request, $response);
    }
}
