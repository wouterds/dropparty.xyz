<?php

namespace DropParty\Application\Http\Handlers\Api\Files;

use DropParty\Application\Filesystem\Filesystem;
use DropParty\Domain\Files\File;
use DropParty\Domain\Files\FileHashIdRepository;
use DropParty\Domain\Files\FileRepository;
use DropParty\Domain\Users\AuthenticatedUser;
use Exception;
use Psr\Http\Message\UploadedFileInterface;
use Respect\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

class AddHandler
{
    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var FileHashIdRepository
     */
    private $fileHashIdRepository;

    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var AuthenticatedUser
     */
    private $authenticatedUser;

    /**
     * @param AuthenticatedUser $authenticatedUser
     * @param FileRepository $fileRepository
     * @param FileHashIdRepository $fileHashIdRepository
     * @param Filesystem $filesystem
     */
    public function __construct(
        AuthenticatedUser $authenticatedUser,
        FileRepository $fileRepository,
        FileHashIdRepository $fileHashIdRepository,
        Filesystem $filesystem
    ) {
        $this->authenticatedUser = $authenticatedUser;
        $this->fileRepository = $fileRepository;
        $this->fileHashIdRepository = $fileHashIdRepository;
        $this->filesystem = $filesystem;
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

        /** @var UploadedFileInterface $uploadedFile */
        $uploadedFile = $request->getUploadedFiles()['file'];

        if (!$this->authenticatedUser->isLoggedIn()) {
            return $response->withStatus(400);
        }

        $file = new File(
            $this->authenticatedUser->getUserId(),
            $uploadedFile->getClientFilename(),
            $uploadedFile->getClientMediaType(),
            $uploadedFile->getSize()
        );

        if (!$this->filesystem->store($file, $uploadedFile->getStream())) {
            return $response->withStatus(400);
        }

        // Short link?
        if (filter_var($request->getParam('short'), FILTER_VALIDATE_BOOLEAN)) {
            $this->fileHashIdRepository->add($file->getId());
            $hashId = $this->fileHashIdRepository->findHashIdByFileId($file->getId());
            $file->setHashId($hashId);
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

        $validator->addRule(Validator::key('uid', Validator::stringType()->notEmpty()));

        if (empty($request->getUploadedFiles()['file'])) {
            throw new Exception('No files provided!');
        }

        $validator->assert($request->getParams());
    }
}
