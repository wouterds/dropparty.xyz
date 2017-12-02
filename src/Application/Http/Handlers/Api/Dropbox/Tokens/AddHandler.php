<?php

namespace DropParty\Application\Http\Handlers\Api\Dropbox\Tokens;

use DropParty\Domain\Dropbox\Token;
use DropParty\Domain\Dropbox\TokenRepository;
use DropParty\Domain\Users\UserId;
use Exception;
use Respect\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

class AddHandler
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
     */
    public function __invoke(Request $request, Response $response): Response
    {
        try {
            $this->validate($request);
        } catch (Exception $e) {
            return $response->withStatus(400);
        }

        $userId = new UserId($request->getParam('user_id'));
        $accessToken = $request->getParam('access_token');

        $token = new Token($userId, $accessToken);

        if ($this->tokenRepository->has($token)) {
            $this->tokenRepository->update($token);
        } else {
            $this->tokenRepository->add($token);
        }

        return $response->withJson([
            'data' => $token,
        ]);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    private function validate(Request $request)
    {
        $validator = Validator::create();

        $validator->addRule(Validator::key('user_id', Validator::stringType()->notEmpty()));
        $validator->addRule(Validator::key('access_token', Validator::stringType()->notEmpty()));

        $validator->assert($request->getParams());
    }
}
