<?php
declare(strict_types=1);

namespace App\Domain\User;

use JsonSerializable;

class User implements JsonSerializable
{
    private int|null $id;
    private string|null $loginId;
    private string|null $loginPw;
    private string $name;
    private string|null $createdAt;
    private string|null $updatedAt;

    /**
     * @param int|null $id
     * @param string|null $loginId
     * @param string|null $loginPw
     * @param string $name
     * @param string|null $createdAt
     * @param string|null $updatedAt
     */
    public function __construct(
        ?int $id,
        ?string $loginId,
        ?string $loginPw,
        string $name,
        ?string $createdAt,
        ?string $updatedAt
    ) {
        $this->id = $id;
        $this->loginId = $loginId;
        $this->loginPw = $loginPw;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getLoginId(): ?string
    {
        return $this->loginId;
    }

    /**
     * @return string|null
     */
    public function getLoginPw(): ?string
    {
        return $this->loginPw;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'login_id' => $this->loginId,
            'name' => $this->name,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
