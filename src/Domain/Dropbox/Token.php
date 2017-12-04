<?php

namespace DropParty\Domain\Dropbox;

use DropParty\Domain\Users\UserId;
use JsonSerializable;

class Token implements JsonSerializable
{
    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @param UserId $userId
     * @param string $accessToken
     */
    public function __construct(UserId $userId, string $accessToken)
    {
        $this->userId = $userId;
        $this->accessToken = $accessToken;
    }

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data)
    {
        return new static(
            new UserId($data['user_id']),
            $data['access_token']
        );
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
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
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }
}
