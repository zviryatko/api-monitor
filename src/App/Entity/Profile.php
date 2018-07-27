<?php
/**
 * @file
 * Contains App\Entity\Profile.
 */

namespace App\Entity;

/**
 * Used for user profile.
 *
 * @Doctrine\ORM\Mapping\Entity
 * @Doctrine\ORM\Mapping\Table(name="profile")
 */
class Profile implements \JsonSerializable
{

    /**
     * @Doctrine\ORM\Mapping\Id
     * @Doctrine\ORM\Mapping\Column(name="id", type="integer")
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="IDENTITY")
     * @var string
     */
    protected $id;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=60, nullable=false, unique=true)
     * @var string
     */
    protected $nickname;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=60, nullable=false, unique=true)
     * @var string
     */
    protected $mail;

    /**
     * @Doctrine\ORM\Mapping\Column(type="array", nullable=false)
     * @var array
     */
    protected $token;

    public function __construct(string $nickname, string $mail, array $token)
    {
        $this->nickname = $nickname;
        $this->mail = $mail;
        $this->token = $token;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'nickname' => $this->nickname,
            'mail' => $this->mail,
            'token' => $this->token,
        ];
    }

    /**
     * @return string
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function nickname(): string
    {
        return $this->nickname;
    }

    /**
     * @return string
     */
    public function mail(): string
    {
        return $this->mail;
    }

    /**
     * @return array
     */
    public function token(): array
    {
        return $this->token;
    }
}