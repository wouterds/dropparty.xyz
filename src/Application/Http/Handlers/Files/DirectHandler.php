<?php

namespace DropParty\Application\Http\Handlers\Files;

use DropParty\Application\ApiClient\DropPartyClient;
use DropParty\Domain\Files\FileAccessLog;
use DropParty\Domain\Files\FileAccessLogRepository;
use DropParty\Domain\Files\FileId;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

class DirectHandler
{
    /**
     * @var DropPartyClient
     */
    private $dropPartyClient;

    /**
     * @var FileAccessLogRepository
     */
    private $fileAccessLogRepository;

    /**
     * @param DropPartyClient $dropPartyClient
     * @param FileAccessLogRepository $fileAccessLogRepository
     */
    public function __construct(DropPartyClient $dropPartyClient, FileAccessLogRepository $fileAccessLogRepository)
    {
        $this->dropPartyClient = $dropPartyClient;
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
        $apiResponse = $this->dropPartyClient->get('/files.download', ['id' => $id]);
        $fileId = new FileId($id);

        if ($apiResponse->getStatusCode() !== 200) {
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

        $response->getBody()->write($apiResponse->getBody()->getContents());
        $response = $response->withHeader('Content-Type', $apiResponse->getHeaderLine('Content-Type'));
        $response = $response->withHeader('Content-Length', $apiResponse->getHeaderLine('Content-Length'));

        return $response;
    }
}
