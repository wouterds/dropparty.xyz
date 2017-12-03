<?php

namespace DropParty\Application\Http\Handlers\Files;

use DropParty\Domain\Files\FileHashIdRepository;
use Slim\Http\Request;
use Slim\Http\Response;

class HashedIdHandler
{
    /**
     * @var FileHashIdRepository
     */
    private $fileHashIdRepository;

    /**
     * @param FileHashIdRepository $fileHashIdRepository
     */
    public function __construct(FileHashIdRepository $fileHashIdRepository)
    {
        $this->fileHashIdRepository = $fileHashIdRepository;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $hashedId
     * @return Response
     */
    public function __invoke(Request $request, Response $response, string $hashedId): Response
    {
        $fileId = $this->fileHashIdRepository->findFileIdByHashId($hashedId);

        if (empty($fileId)) {
            return $response->withStatus(400);
        }

        if (filter_var($request->getParam('direct'), FILTER_VALIDATE_BOOLEAN)) {
            return $response->withRedirect('/direct/' . $fileId);
        }

        if (filter_var($request->getParam('download'), FILTER_VALIDATE_BOOLEAN)) {
            return $response->withRedirect('/download/' . $fileId);
        }

        return $response->withRedirect('/view/' . $fileId);
    }
}
