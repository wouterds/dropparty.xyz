<?php

namespace DropParty\Application\Http\Handlers\Integrations;

use DropParty\Application\Oauth\DropboxOauthProvider;
use DropParty\Domain\Dropbox\Token;
use DropParty\Domain\Dropbox\TokenRepository;
use DropParty\Domain\Users\UserId;
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
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * @param DropboxOauthProvider $dropboxOauthProvider
     * @param TokenRepository $tokenRepository
     */
    public function __construct(DropboxOauthProvider $dropboxOauthProvider, TokenRepository $tokenRepository)
    {
        $this->oauthProvider = $dropboxOauthProvider;
        $this->tokenRepository = $tokenRepository;
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

        $token = new Token(
            new UserId($request->getCookieParam('uid')),
            $accessToken->getToken()
        );

        if ($this->tokenRepository->has($token)) {
            $this->tokenRepository->update($token);
        } else {
            $this->tokenRepository->add($token);
        }

        return $response->withRedirect('/account?integration-installed=dropbox');
    }
}
