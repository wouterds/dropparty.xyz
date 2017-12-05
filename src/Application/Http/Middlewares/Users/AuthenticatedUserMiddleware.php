<?php

namespace DropParty\Application\Http\Middlewares\Users;

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
     * @var Container
     */
    private $container;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param Container $container
     * @param UserRepository $userRepository
     */
    public function __construct(Container $container, UserRepository $userRepository)
    {
        $this->container = $container;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param Route $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, Route $next): Response
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

        $this->container->share(AuthenticatedUser::class, new AuthenticatedUser($user));

        return $next($request, $response);
    }
}
