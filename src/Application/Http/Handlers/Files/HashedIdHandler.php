<?php

namespace DropParty\Application\Http\Handlers\Files;

use DropParty\Domain\Files\FileHashidRepository;
use Slim\Http\Request;
use Slim\Http\Response;

class HashedIdHandler
{
    /**
     * @var FileHashidRepository
     */
    private $fileHashidRepository;

    /**
     * @param FileHashidRepository $fileHashidRepository
     */
    public function __construct(FileHashidRepository $fileHashidRepository)
    {
        $this->fileHashidRepository = $fileHashidRepository;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $hashedId
     * @return Response
     */
    public function __invoke(Request $request, Response $response, string $hashedId): Response
    {
        $fileId = $this->fileHashidRepository->findFileIdByHashId($hashedId);

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
