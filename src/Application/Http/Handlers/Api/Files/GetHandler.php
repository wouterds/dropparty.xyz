<?php

namespace DropParty\Application\Http\Handlers\Api\Files;

use DropParty\Domain\Files\FileHashidRepository;
use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use Exception;
use Respect\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

class GetHandler
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var FileHashidRepository
     */
    private $fileHashidRepository;

    /**
     * @param FileRepository $fileRepository
     * @param FileHashidRepository $fileHashidRepository
     */
    public function __construct(FileRepository $fileRepository, FileHashidRepository $fileHashidRepository)
    {
        $this->fileRepository = $fileRepository;
        $this->fileHashidRepository = $fileHashidRepository;
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

        $id = $request->getParam('id');

        $fileId = null;
        if (strlen($id) !== 32) {
            $fileId = $this->fileHashidRepository->findFileIdByHashId($id);
        }

        if (empty($fileId)) {
            $fileId = new FileId($id);
        }

        $file = $this->fileRepository->find($fileId);

        if (empty($file)) {
            return $response->withStatus(400);
        }

        return $response->withJson([
            'data' => $file,
        ]);
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    private function validate(Request $request)
    {
        $validator = Validator::create();

        $validator->addRule(Validator::key('id', Validator::stringType()->notEmpty()));

        $validator->assert($request->getParams());
    }
}
