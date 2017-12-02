<?php

namespace DropParty\Application\Http\Handlers\Files;

use DropParty\Application\ApiClient\DropPartyClient;
use Slim\Http\Request;
use Slim\Http\Response;

class HashedIdHandler
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
     * @param string $hashedId
     * @return Response
     */
    public function __invoke(Request $request, Response $response, string $hashedId): Response
    {
        $apiResponse = $this->dropPartyClient->get('/files.get', ['id' => $hashedId]);

        if ($apiResponse->getStatusCode() !== 200) {
            return $response->withStatus(400);
        }

        $contents = json_decode((string) $apiResponse->getBody(), true);

        $id = $contents['data']['id'];

        if (filter_var($request->getParam('direct'), FILTER_VALIDATE_BOOLEAN)) {
            return $response->withRedirect('/direct/' . $id);
        }

        if (filter_var($request->getParam('download'), FILTER_VALIDATE_BOOLEAN)) {
            return $response->withRedirect('/download/' . $id);
        }

        return $response->withRedirect('/view/' . $id);
    }
}
