<?php

namespace DropParty\Application\Http\Handlers\Integrations;

use DropParty\Domain\Dropbox\TokenRepository;
use DropParty\Domain\Users\UserId;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

class DropboxUnlinkHandler
{
    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * @param TokenRepository $tokenRepository
     */
    public function __construct(TokenRepository $tokenRepository)
    {
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
        $token = $this->tokenRepository->findActiveTokenForUserId(new UserId($request->getCookieParam('uid')));

        if (!empty($token)) {
            $this->tokenRepository->delete($token);
        }

        return $response->withRedirect('/account');
    }
}
