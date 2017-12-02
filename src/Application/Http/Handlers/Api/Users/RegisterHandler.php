<?php

namespace DropParty\Application\Http\Handlers\Api\Users;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use DropParty\Domain\Users\User;
use DropParty\Domain\Users\UserRepository;
use Exception;
use Respect\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

class RegisterHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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
            return $response->withStatus(400);
        }

        $user = new User($request->getParam('email'), $request->getParam('password'));
        $user->setName($request->getParam('name'));

        try {
            $this->userRepository->add($user);
        } catch (UniqueConstraintViolationException $e) {
            return $response->withStatus(400);
        }

        return $response->withJson([
            'data' => $user,
        ]);
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
