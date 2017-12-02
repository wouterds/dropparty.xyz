<?php

namespace DropParty\Application\Http\Handlers\Files;

use DropParty\Application\ApiClient\DropPartyClient;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

class DownloadHandler
{
    /**
     * @var DropPartyClient
     */
    private $dropPartyClient;

    /**
     * @param DropPartyClient $dropPartyClient
     */
    public function __construct(DropPartyClient $dropPartyClient)
    {
        $this->dropPartyClient = $dropPartyClient;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function __invoke(Request $request, Response $response, string $id): Response
    {
        $apiResponse = $this->dropPartyClient->get('/files.get', ['id' => $id]);

        if ($apiResponse->getStatusCode() !== 200) {
            return $response->withStatus(400);
        }

        $contents = json_decode((string) $apiResponse->getBody(), true);

        if (empty($contents['data'])) {
            return $response->withStatus(400);
        }

        $file = $contents['data'];

        $apiResponse = $this->dropPartyClient->get('/files.download', ['id' => $id]);

        if ($apiResponse->getStatusCode() !== 200) {
            return $response->withStatus(400);
        }

        $ip = $request->getServerParam('REMOTE_ADDR');
        if (!empty($request->getHeaderLine('CF-Connecting-IP'))) {
            $ip = $request->getHeaderLine('CF-Connecting-IP');
        }
        $userAgent = $request->getServerParam('HTTP_USER_AGENT');
        $referrer = $request->getServerParam('HTTP_REFERER');

        try {
            $this->dropPartyClient->post('/files.access-log.add', [
                'file_id' => $id,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'referrer' => $referrer,
            ]);
        } catch (Exception $e) {
            // TODO
        }

        $response = $response->withHeader('Content-Description', 'File Transfer');
        $response = $response->withHeader('Content-Type', 'application/octet-stream');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename=' . basename($file['name']));
        $response = $response->withHeader('Content-Transfer-Encoding', 'binary');
        $response = $response->withHeader('Connection', 'Keep-Alive');
        $response = $response->withHeader('Content-Length', $apiResponse->getBody()->getSize());
        $response = $response->withBody($apiResponse->getBody());

        return $response;
    }
}
