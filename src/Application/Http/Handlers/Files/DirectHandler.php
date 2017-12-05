<?php

namespace DropParty\Application\Http\Handlers\Files;

use DropParty\Application\Filesystem\Filesystem;
use DropParty\Domain\Files\FileAccessLogRepository;
use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use Slim\Http\Request;
use Slim\Http\Response;

class DirectHandler
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var FileAccessLogRepository
     */
    private $fileAccessLogRepository;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param FileRepository $fileRepository
     * @param FileAccessLogRepository $fileAccessLogRepository
     * @param Filesystem $filesystem
     */
    public function __construct(
        FileRepository $fileRepository,
        FileAccessLogRepository $fileAccessLogRepository,
        Filesystem $filesystem
    ) {
        $this->fileRepository = $fileRepository;
        $this->fileAccessLogRepository = $fileAccessLogRepository;
        $this->filesystem = $filesystem;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function __invoke(Request $request, Response $response, string $id): Response
    {
        $fileId = new FileId($id);
        $file = $this->fileRepository->find($fileId);

        if (empty($file)) {
            return $response->withStatus(400);
        }

        $response = $response->withHeader('Content-Type', $file->getContentType());
        $response = $response->withHeader('Content-Length', $file->getSize());
        $response = $response->withBody($this->filesystem->get($file));

        return $response;
    }
}
