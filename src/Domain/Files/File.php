<?php

namespace DropParty\Domain\Files;

use DropParty\Domain\Users\UserId;
use JsonSerializable;

class File implements JsonSerializable
{
    /**
     * @var FileId
     */
    private $id;

    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var int
     */
    private $size;

    /**
     * @var string
     */
    private $md5;

    /**
     * @var string
     */
    private $hashId;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @param UserId $userId
     * @param string $name
     * @param string $contentType
     * @param int $size
     */
    public function __construct(UserId $userId, string $name, string $contentType, int $size)
    {
        $this->id = new FileId();
        $this->userId = $userId;
        $this->name = $name;
        $this->contentType = $contentType;
        $this->size = $size;
    }

    /**
     * @param array $data
     * @return File
     */
    public static function fromArray(array $data): File
    {
        $file = new File(
            new UserId($data['user_id']),
            $data['name'],
            $data['content_type'],
            $data['size']
        );
        $file->id = new FileId(!empty($data['id']) ? $data['id'] : null);
        $file->md5 = !empty($data['md5']) ? $data['md5'] : null;
        $file->hashId = !empty($data['hash_id']) ? $data['hash_id'] : null;
        $file->createdAt = !empty($data['created_at']) ? $data['created_at'] : null;

        return $file;
    }

    /**
     * @return FileId
     */
    public function getId(): FileId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFolderPath(): string
    {
        return APP_DIR . getenv('FILESYSTEM_DIR') . '/' . $this->getUserId();
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->getFolderPath() . '/' . $this->getId();
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getMd5(): string
    {
        return $this->md5;
    }

    /**
     * @param string $md5
     */
    public function setMd5(string $md5)
    {
        $this->md5 = $md5;
    }

    /**
     * @return string
     */
    public function getHashId(): string
    {
        return $this->hashId;
    }

    /**
     * @param string $hashId
     */
    public function setHashId(string $hashId)
    {
        $this->hashId = $hashId;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
