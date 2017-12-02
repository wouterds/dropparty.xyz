<?php

namespace DropParty\Domain\Files;

class FileAccessLog
{
    /**
     * @var FileId
     */
    private $fileId;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string|null
     */
    private $userAgent;

    /**
     * @var string|null
     */
    private $referrer;

    /**
     * @param FileId $fileId
     * @param string $ip
     * @param string $userAgent
     * @param string $referrer
     */
    public function __construct(FileId $fileId, string $ip, string $userAgent = null, string $referrer = null)
    {
        $this->fileId = $fileId;
        $this->ip = $ip;
        $this->userAgent = $userAgent;
        $this->referrer = $referrer;
    }

    /**
     * @return FileId
     */
    public function getFileId(): FileId
    {
        return $this->fileId;
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return null|string
     */
    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    /**
     * @return null|string
     */
    public function getReferrer(): ?string
    {
        return $this->referrer;
    }
}
