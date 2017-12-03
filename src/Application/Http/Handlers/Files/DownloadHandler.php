<?php

namespace DropParty\Application\Http\Handlers\Files;

use DropParty\Domain\Files\FileAccessLogRepository;
use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
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
     * @var FileAccessLogRepository
     */
    private $fileAccessLogRepository;

    /**
     * @param FileRepository $fileRepository
     * @param FileAccessLogRepository $fileAccessLogRepository
     */
    public function __construct(FileRepository $fileRepository, FileAccessLogRepository $fileAccessLogRepository)
    {
        $this->fileRepository = $fileRepository;
        $this->fileAccessLogRepository = $fileAccessLogRepository;
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

        $response = $response->withHeader('Content-Description', 'File Transfer');
        $response = $response->withHeader('Content-Type', 'application/octet-stream');
        $response = $response->withHeader('Content-Disposition', 'attachment; filename=' . basename($file['name']));
        $response = $response->withHeader('Content-Transfer-Encoding', 'binary');
        $response = $response->withHeader('Connection', 'Keep-Alive');
        $response = $response->withHeader('Content-Length', $file->getSize());
        $response = $response->withBody(new Stream(fopen($file->getPath(), 'r')));

        return $response;
    }
}
