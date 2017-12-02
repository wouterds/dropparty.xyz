<?php

namespace DropParty\Application\Http\Handlers\Api\Files;

use DropParty\Domain\Files\File;
use DropParty\Domain\Files\FileHashidRepository;
use DropParty\Domain\Files\FileRepository;
use DropParty\Domain\Users\UserId;
use DropParty\Domain\Users\UserRepository;
use Exception;
use Psr\Http\Message\UploadedFileInterface;
use Respect\Validation\Validator;
use Slim\Http\Request;
use Slim\Http\Response;

class AddHandler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var FileRepository
     */
    private $fileRepository;

    /**
     * @var FileHashidRepository
     */
    private $fileHashidRepository;

    /**
     * @param UserRepository $userRepository
     * @param FileRepository $fileRepository
     * @param FileHashidRepository $fileHashidRepository
     */
    public function __construct(UserRepository $userRepository, FileRepository $fileRepository, FileHashidRepository $fileHashidRepository)
    {
        $this->userRepository = $userRepository;
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

        $uid = $request->getParam('uid');
        /** @var UploadedFileInterface $uploadedFile */
        $uploadedFile = $request->getUploadedFiles()['file'];

        $user = $this->userRepository->find(new UserId($uid));

        if (empty($user)) {
            return $response->withStatus(400);
        }

        $file = new File(
            $user->getId(),
            $uploadedFile->getClientFilename(),
            $uploadedFile->getClientMediaType(),
            $uploadedFile->getSize()
        );

        if (!file_exists($file->getFolderPath())) {
            mkdir($file->getFolderPath());
        }

        $written = file_put_contents($file->getPath(), $uploadedFile->getStream());

        if ($written === false) {
            return $response->withStatus(400);
        }

        $file->setMd5(md5_file($file->getPath()));

        try {
            $this->fileRepository->add($file);
        } catch (Exception $e) {
            unlink($file->getPath());
            return $response->withStatus(400);
        }

        // Short link?
        if (filter_var($request->getParam('short'), FILTER_VALIDATE_BOOLEAN)) {
            $this->fileHashidRepository->add($file->getId());
            $hashId = $this->fileHashidRepository->findHashIdByFileId($file->getId());
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
