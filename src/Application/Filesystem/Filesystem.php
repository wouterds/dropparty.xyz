<?php

namespace DropParty\Application\Filesystem;

use DropParty\Domain\Dropbox\TokenRepository as DropboxTokenRepository;
use DropParty\Domain\Files\File;
use DropParty\Domain\Files\FileAccessLog;
use DropParty\Domain\Files\FileAccessLogRepository;
use DropParty\Domain\Files\FileId;
use DropParty\Domain\Files\FileRepository;
use DropParty\Infrastructure\Cache\Cache;
use Exception;
use League\Flysystem\FileNotFoundException;
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
     * @var Cache
     */
    private $cache;

    /**
     * @var DropboxTokenRepository
     */
    private $dropboxTokenRepository;

    /**
     * @param DropboxTokenRepository $dropboxTokenRepository
     * @param LocalFilesystem $localFilesystem
     * @param DropboxFilesystem|null $dropboxFilesystem
     * @param FileRepository $fileRepository
     * @param FileAccessLogRepository $fileAccessLogRepository
     * @param Cache $cache
     * @param Request $request
     */
    public function __construct(
        DropboxTokenRepository $dropboxTokenRepository,
        LocalFilesystem $localFilesystem,
        DropboxFilesystem $dropboxFilesystem = null,
        FileRepository $fileRepository,
        FileAccessLogRepository $fileAccessLogRepository,
        Cache $cache,
        Request $request
    ) {
        $this->fileRepository = $fileRepository;
        $this->request = $request;
        $this->fileAccessLogRepository = $fileAccessLogRepository;
        $this->cache = $cache;
        $this->dropboxTokenRepository = $dropboxTokenRepository;
        $this->localFilesystem = $localFilesystem;
        $this->dropboxFilesystem = $dropboxFilesystem;
    }

    /**
     * @param File $file
     * @param StreamInterface $stream
     * @return bool
     * @throws FileNotFoundException
     */
    public function store(File $file, StreamInterface $stream): bool
    {
        $filesystem = $this->filesystemForFile($file);

        if (get_class($filesystem) === DropboxFilesystem::class) {
            $file->setFilesystem(File::FILESYSTEM_DROPBOX);
        } else {
            $file->setFilesystem(File::FILESYSTEM_LOCAL);
        }

        $this->cacheStore($file->getId(), $stream);

        if ($filesystem->putStream($file->getPath(), $stream->detach()) === false) {
            return false;
        }

        $file->setMd5($filesystem->hash($file->getPath(), 'md5'));

        try {
            $this->fileRepository->add($file);
        } catch (Exception $e) {
            $filesystem->delete($file->getPath());
            return false;
        }

        return true;
    }

    /**
     * @param File $file
     * @return StreamInterface
     * @throws FileNotFoundException
     */
    public function get(File $file): StreamInterface
    {
        $stream = $this->cacheGet($file->getId());

        if (empty($stream)) {
            $filesystem = $this->filesystemForFile($file);
            $stream = new Stream($filesystem->readStream($file->getPath()));
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

        $this->cacheStore($file->getId(), $stream);

        return $stream;
    }

    /**
     * @param File $file
     * @return FilesystemInterface
     */
    private function filesystemForFile(File $file): FilesystemInterface
    {
        if ($file->getFilesystem() === File::FILESYSTEM_DROPBOX || empty($file->getFilesystem())) {
            $dropboxToken = $this->dropboxTokenRepository->findActiveTokenForUserId($file->getUserId());

            if ($dropboxToken) {
                return DropboxFilesystem::filesystemForDropboxToken($dropboxToken);
            }
        }

        return $this->localFilesystem;
    }

    /**
     * @param FileId $fileId
     * @param StreamInterface $stream
     */
    private function cacheStore(FileId $fileId, StreamInterface $stream)
    {
        // Bigger than 2.5MB? Don't cache
        if ($stream->getSize() > 1024 * 1024 * 2.5) {
            return;
        }

        $item = $this->cache->getItem('files.' . $fileId);

        if (!$this->cache->has($item->getKey())) {
            $item->set($stream->getContents());
        }

        $item->expiresAfter(strtotime('5 minutes'));
        $this->cache->save($item);
    }

    /**
     * @param FileId $fileId
     * @return StreamInterface|null
     */
    private function cacheGet(FileId $fileId): ?StreamInterface
    {
        $item = $this->cache->getItem('files.' . $fileId);

        if (!$this->cache->has($item->getKey())) {
            return null;
        }

        $tmp = tmpfile();
        fwrite($tmp, $item->get());
        return new Stream($tmp);
    }
}
