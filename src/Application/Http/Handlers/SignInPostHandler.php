<?php

namespace DropParty\Application\Http\Handlers;

use DropParty\Domain\Users\User;
use DropParty\Domain\Users\UserRepository;
use DropParty\Infrastructure\ApplicationMonitor\ApplicationMonitor;
use DropParty\Infrastructure\Http\Handlers\AbstractViewHandler;
use DropParty\Infrastructure\View\Twig;
use Exception;
use Respect\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

class SignInPostHandler extends AbstractViewHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param Twig $twig
     * @param ApplicationMonitor $applicationMonitor
     * @param UserRepository $userRepository
     */
    public function __construct(Twig $twig, ApplicationMonitor $applicationMonitor, UserRepository $userRepository)
    {
        parent::__construct($twig, $applicationMonitor);
        $this->userRepository = $userRepository;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return 'sign-in.html.twig';
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $this->validate($request);
        } catch (Exception $e) {
            return $this->render($request, $response, ['failed' => true]);
        }

        $user = $this->userRepository->findByEmail($request->getParam('email'));

        if (empty($user)) {
            return $this->render($request, $response, ['failed' => true]);
        }

        $password = User::hashPassword($user->getSalt(), $request->getParam('password'));
        if ($password !== $user->getPassword()) {
            return $this->render($request, $response, ['failed' => true]);
        }

        $days = 60 * 60 * 24;
        $days = $days * 30;
        setcookie('uid', $user->getId(), time() + $days);

        return $response->withRedirect('/files');
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    private function validate(Request $request)
    {
        $validator = Validator::create();

        $validator->addRule(Validator::key('email', Validator::stringType()->notEmpty()->email()));
        $validator->addRule(Validator::key('password', Validator::stringType()->notEmpty()));

        $validator->assert($request->getParams());
    }
}
