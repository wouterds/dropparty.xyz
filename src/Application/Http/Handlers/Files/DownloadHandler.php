<?php

namespace DropParty\Application\Http\Handlers\Files;

use DropParty\Application\ApiClient\DropPartyClient;
use DropParty\Domain\Files\FileAccessLog;
use DropParty\Domain\Files\FileAccessLogRepository;
use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;

class DownloadHandler
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
     * @param string $id
     * @return Response
     */
    public function __invoke(Request $request, Response $response, string $id): Response
    {
        $fileId = new FileId($id);
        $file = $this->fileRepository->find($fileId);

        if (empty($file)) {
            return $response->withStatus(400);
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

        $response = $response->withHeader('Content-Description', 'File Transfer');
        $response = $response->withHeader('Content-Type', 'application/octet-stream');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename=' . basename($file['name']));
        $response = $response->withHeader('Content-Transfer-Encoding', 'binary');
        $response = $response->withHeader('Connection', 'Keep-Alive');
        $response = $response->withHeader('Content-Length', $file->getSize());
        $response = $response->withBody(new Stream(fopen($file->getPath(), 'r')));

        return $response;
    }
}
