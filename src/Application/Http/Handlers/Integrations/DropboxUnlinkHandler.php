<?php

namespace DropParty\Application\Http\Handlers\Integrations;

use DropParty\Domain\Dropbox\TokenRepository;
use DropParty\Domain\Users\AuthenticatedUser;
use DropParty\Domain\Users\UserId;
use Exception;
use Slim\Http\Request;
use Slim\Http\Response;

class DropboxUnlinkHandler
{
    /**
     * @var AuthenticatedUser
     */
    private $authenticatedUser;

    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    /**
     * @param AuthenticatedUser $authenticatedUser
     * @param TokenRepository $tokenRepository
     */
    public function __construct(AuthenticatedUser $authenticatedUser, TokenRepository $tokenRepository)
    {
        $this->authenticatedUser = $authenticatedUser;
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
        $token = $this->tokenRepository->findActiveTokenForUserId($this->authenticatedUser->getUser()->getId());

        if (!empty($token)) {
            $this->tokenRepository->delete($token);
        }

        return $response->withRedirect('/account');
    }
}
