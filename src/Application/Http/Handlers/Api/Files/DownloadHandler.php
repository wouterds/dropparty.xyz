<?php

namespace DropParty\Application\Http\Handlers\Api\Files;

use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use Exception;
use Respect\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;

class DownloadHandler
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @param FileRepository $fileRepository
     */
    public function __construct(FileRepository $fileRepository)
    {
        $this->fileRepository = $fileRepository;
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
        $file = $this->fileRepository->find(new FileId($id));

        if (empty($file)) {
            return $response->withStatus(400);
        }

        $response = $response->withHeader('Content-Type', $file->getContentType());
        $response = $response->withHeader('Content-Length', $file->getSize());
        $response = $response->withBody(new Stream(fopen($file->getPath(), 'r')));

        return $response;
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
