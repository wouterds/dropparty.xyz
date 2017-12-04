<?php

namespace DropParty\Application\Http\Middlewares\Files;

use DropParty\Application\Http\Handlers\Files\DirectHandler;
use DropParty\Application\Http\Handlers\Files\DownloadHandler;
use DropParty\Domain\Files\FileAccessLog;
use DropParty\Domain\Files\FileAccessLogRepository;
use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Http\Request;
use Slim\Route;

class AccessLogMiddleware
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var FileAccessLogRepository
     */
    private $fileAccessLogRepository;

    /**
     * @param FileRepository $fileRepository
     * @param FileAccessLogRepository $fileAccessLogRepository
     */
    public function __construct(FileRepository $fileRepository, FileAccessLogRepository $fileAccessLogRepository)
    {
        $this->fileRepository = $fileRepository;
        $this->fileAccessLogRepository = $fileAccessLogRepository;
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
        if (in_array($next->getCallable(), [DirectHandler::class, DownloadHandler::class]) === false) {
            return $next($request, $response);
        }

        $fileId = new FileId($next->getArgument('id'));
        if ($this->fileRepository->has($fileId) === false) {
            return $next($request, $response);
        }

        $ip = $request->getServerParam('REMOTE_ADDR');
        if (!empty($request->getHeaderLine('CF-Connecting-IP'))) {
            $ip = $request->getHeaderLine('CF-Connecting-IP');
        }

        $userAgent = $request->getServerParam('HTTP_USER_AGENT');
        $referrer = $request->getServerParam('HTTP_REFERER');

        $fileAccessLog = new FileAccessLog(
            $fileId,
            $ip,
            !empty($userAgent) ? $userAgent : null,
            !empty($referrer) ? $referrer : null
        );

        $this->fileAccessLogRepository->add($fileAccessLog);

        return $next($request, $response);
    }
}
