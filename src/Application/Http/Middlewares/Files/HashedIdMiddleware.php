<?php

namespace DropParty\Application\Http\Middlewares\Files;

use DropParty\Application\Http\Handlers\Files\DirectHandler;
use DropParty\Application\Http\Handlers\Files\DownloadHandler;
use DropParty\Domain\Files\FileAccessLog;
use DropParty\Domain\Files\FileAccessLogRepository;
use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use Exception;
use Hashids\Hashids;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\Request;
use Slim\Route;
use Teapot\StatusCode;

class HashedIdMiddleware
{
    /**
     * @var Hashids
     */
    private $hashids;

    /**
     * @param Hashids $hashids
     */
    public function __construct(Hashids $hashids)
    {
        $this->hashids = $hashids;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Route $next
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response, Route $next): Response
    {
        if (empty($next->getArgument('hashedId'))) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        if (empty($this->hashids->decode($next->getArgument('hashedId')))) {
            return $response->withStatus(StatusCode::NOT_FOUND);
        }

        return $next($request, $response);
    }
}
