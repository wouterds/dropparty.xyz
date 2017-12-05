<?php

namespace DropParty\Application\Filesystem;

use DropParty\Domain\Dropbox\TokenRepository as DropboxTokenRepository;
use DropParty\Domain\Files\File;
use DropParty\Domain\Files\FileAccessLog;
use DropParty\Domain\Files\FileAccessLogRepository;
use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use DropParty\Domain\Users\AuthenticatedUser;
use DropParty\Domain\Users\UserRepository;
use Exception;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Request;
use Slim\Http\Stream;

class Filesystem
{
    /**
     * @var string
     */
    private $defaultFilesystemType;

    /**
     * @var LocalFilesystem
     */
    private $localFilesystem;

    /**
     * @var DropboxFilesystem|null
     */
    private $dropboxFilesystem;

    /**
     * @var FilesystemInterface
     */
    private $defaultFilesystem;

    /**
     * @var FileRepository
     */
    private $fileRepository;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var FileAccessLogRepository
     */
    private $fileAccessLogRepository;

    /**
     * @param AuthenticatedUser $authenticatedUser
     * @param DropboxTokenRepository $dropboxTokenRepository
     * @param LocalFilesystem $localFilesystem
     * @param DropboxFilesystem|null $dropboxFilesystem
     * @param FileRepository $fileRepository
     * @param FileAccessLogRepository $fileAccessLogRepository
     * @param Request $request
     */
    public function __construct(
        AuthenticatedUser $authenticatedUser,
        DropboxTokenRepository $dropboxTokenRepository,
        LocalFilesystem $localFilesystem,
        DropboxFilesystem $dropboxFilesystem = null,
        FileRepository $fileRepository,
        FileAccessLogRepository $fileAccessLogRepository,
        Request $request
    ) {
        $this->localFilesystem = $localFilesystem;
        $this->dropboxFilesystem = $dropboxFilesystem;
        $this->defaultFilesystemType = File::FILESYSTEM_LOCAL;
        $this->defaultFilesystem = $localFilesystem;

        $activeDropboxToken = $dropboxTokenRepository->findActiveTokenForUserId(
            $authenticatedUser->getUser()->getId()
        );

        if (!empty($activeDropboxToken)) {
            $this->defaultFilesystemType = File::FILESYSTEM_DROPBOX;
            $this->defaultFilesystem = $dropboxFilesystem;
        }

        $this->fileRepository = $fileRepository;
        $this->request = $request;
        $this->fileAccessLogRepository = $fileAccessLogRepository;
    }

    /**
     * @param File $file
     * @param StreamInterface $stream
     * @return bool
     */
    public function store(File $file, StreamInterface $stream): bool
    {
        if ($this->defaultFilesystem->putStream($file->getPath(), $stream->detach()) === false) {
            return false;
        }

        $file->setFilesystem($this->defaultFilesystemType);
        $file->setMd5($this->defaultFilesystem->hash($file->getPath(), 'md5'));

        try {
            $this->fileRepository->add($file);
        } catch (Exception $e) {
            $this->defaultFilesystem->delete($file->getPath());
            return false;
        }

        return true;
    }

    /**
     * @param File $file
     * @return StreamInterface
     */
    public function get(File $file): StreamInterface
    {
        $fileSystem = $this->localFilesystem;
        if ($file->getFilesystem() === File::FILESYSTEM_DROPBOX && $this->dropboxFilesystem) {
            $fileSystem = $this->dropboxFilesystem;
        }

        $ip = $this->request->getServerParam('REMOTE_ADDR');
        if (!empty($this->request->getHeaderLine('CF-Connecting-IP'))) {
            $ip = $this->request->getHeaderLine('CF-Connecting-IP');
        }

        $userAgent = $this->request->getServerParam('HTTP_USER_AGENT');
        $referrer = $this->request->getServerParam('HTTP_REFERER');

        $fileAccessLog = new FileAccessLog(
            $file->getId(),
            $ip,
            !empty($userAgent) ? $userAgent : null,
            !empty($referrer) ? $referrer : null
        );

        $this->fileAccessLogRepository->add($fileAccessLog);

        return new Stream($fileSystem->readStream($file->getPath()));
    }
}
