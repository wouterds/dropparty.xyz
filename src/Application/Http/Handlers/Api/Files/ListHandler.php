<?php

namespace DropParty\Application\Http\Handlers\Api\Files;

use DropParty\Domain\Files\FileRepository;
use DropParty\Domain\Users\UserId;
use Exception;
use Hashids\Hashids;
use Respect\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

class ListHandler
{
    /**
     * @var FileRepository
     */
    private $fileRepository;
    /**
     * @var Hashids
     */
    private $hashids;

    /**
     * @param FileRepository $fileRepository
     */
    public function __construct(FileRepository $fileRepository, Hashids $hashids)
    {
        $this->fileRepository = $fileRepository;
        $this->hashids = $hashids;
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

        $uid = $request->getParam('uid');
        $files = $this->fileRepository->findByUserId(new UserId($uid));

        if (empty($files)) {
            return $response->withStatus(400);
        }

        return $response->withJson([
            'data' => $files,
        ]);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    private function validate(Request $request)
    {
        $validator = Validator::create();

        $validator->addRule(Validator::key('uid', Validator::stringType()->notEmpty()));

        $validator->assert($request->getParams());
    }
}
