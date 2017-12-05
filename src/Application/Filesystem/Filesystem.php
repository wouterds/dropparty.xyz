<?php

namespace DropParty\Application\Filesystem;

use DropParty\Domain\Dropbox\TokenRepository as DropboxTokenRepository;
use DropParty\Domain\Files\File;
use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use DropParty\Domain\Users\AuthenticatedUser;
use DropParty\Domain\Users\UserRepository;
use Exception;
use League\Flysystem\FilesystemInterface;
use Psr\Http\Message\StreamInterface;
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
     * @param AuthenticatedUser $authenticatedUser
     * @param DropboxTokenRepository $dropboxTokenRepository
     * @param LocalFilesystem $localFilesystem
     * @param DropboxFilesystem|null $dropboxFilesystem
     * @param FileRepository $fileRepository
     */
    public function __construct(
        AuthenticatedUser $authenticatedUser,
        DropboxTokenRepository $dropboxTokenRepository,
        LocalFilesystem $localFilesystem,
        DropboxFilesystem $dropboxFilesystem = null,
        FileRepository $fileRepository
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

        return new Stream($fileSystem->readStream($file->getPath()));
    }
}
