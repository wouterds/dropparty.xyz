<?php

namespace DropParty\Application\Http\Handlers\Integrations;

use DropParty\Application\ApiClient\DropPartyClient;
use DropParty\Application\Oauth\DropboxOauthProvider;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

class DropboxHandler
{
    /**
     * @var DropboxOauthProvider
     */
    private $oauthProvider;

    /**
     * @var DropPartyClient
     */
    private $dropPartyClient;

    /**
     * @param DropboxOauthProvider $dropboxOauthProvider
     * @param DropPartyClient $dropPartyClient
     */
    public function __construct(DropboxOauthProvider $dropboxOauthProvider, DropPartyClient $dropPartyClient)
    {
        $this->oauthProvider = $dropboxOauthProvider;
        $this->dropPartyClient = $dropPartyClient;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        if (!empty($request->getQueryParam('error'))) {
            throw new Exception('Error');
        }

        if (empty($request->getQueryParam('code'))) {
            return $response->withRedirect($this->oauthProvider->getAuthorizationUrl());
        }

        $accessToken = $this->oauthProvider->getAccessToken('authorization_code', [
            'code' => $request->getQueryParam('code'),
        ]);

        $this->dropPartyClient->post('/dropbox/tokens.add', [
            'user_id' => $request->getCookieParam('uid'),
            'access_token' => $accessToken->getToken(),
        ]);

        return $response->withRedirect('/account?integration-installed=dropbox');
    }
}
