<?php

namespace DropParty\Application\Http\Handlers;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use DropParty\Domain\Users\User;
use DropParty\Domain\Users\UserRepository;
use DropParty\Infrastructure\ApplicationMonitor\ApplicationMonitor;
use DropParty\Infrastructure\Http\Handlers\AbstractViewHandler;
use DropParty\Infrastructure\View\Twig;
use Exception;
use Respect\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

class SignUpPostHandler extends AbstractViewHandler
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
        return 'sign-up.html.twig';
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

        $user = new User($request->getParam('email'), $request->getParam('password'));
        $user->setName($request->getParam('name'));

        try {
            $this->userRepository->add($user);
        } catch (UniqueConstraintViolationException $e) {
            return $this->render($request, $response, ['failed' => true]);
        }

        $days = 60 * 60 * 24;
        $days = $days * 30;
        setcookie('uid', $user->getId(), time() + $days);

        return $response->withRedirect('/');
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    private function validate(Request $request)
    {
        $validator = Validator::create();

        $validator->addRule(Validator::key('name', Validator::stringType()->notEmpty()));
        $validator->addRule(Validator::key('email', Validator::stringType()->notEmpty()->email()));
        $validator->addRule(Validator::key('password', Validator::stringType()->notEmpty()));

        $validator->assert($request->getParams());
    }
}
