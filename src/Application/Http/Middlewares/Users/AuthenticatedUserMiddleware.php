<?php

namespace DropParty\Application\Http\Middlewares\Users;

use Closure;
use DropParty\Application\Container;
use DropParty\Domain\Users\AuthenticatedUser;
use DropParty\Domain\Users\UserId;
use DropParty\Domain\Users\UserRepository;
use Slim\Http\Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Route;

class AuthenticatedUserMiddleware
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var AuthenticatedUser
     */
    private $authenticatedUser;

    /**
     * @param UserRepository $userRepository
     * @param AuthenticatedUser $authenticatedUser
     */
    public function __construct(UserRepository $userRepository, AuthenticatedUser $authenticatedUser)
    {
        $this->userRepository = $userRepository;
        $this->authenticatedUser = $authenticatedUser;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Route|Closure $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $next): Response
    {
        $userId = null;

        if (!empty($request->getCookieParam('uid'))) {
            $userId = new UserId($request->getCookieParam('uid'));
        }

        if (!empty($request->getParam('uid'))) {
            $userId = new UserId($request->getParam('uid'));
        }

        $user = null;
        if (!empty($userId)) {
            $user = $this->userRepository->find($userId);
        }

        if ($user) {
            $this->authenticatedUser->setUser($user);
        }

        return $next($request, $response);
    }
}
