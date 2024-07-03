<?php
/**
 * @file
 * Contains App\Entity\Profile.
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "profile")]
class Profile implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;

    #[ORM\Column(type: "string", length: 60, nullable: false, unique: true)]
    private $nickname;

    #[ORM\Column(type: "string", length: 60, nullable: false, unique: true)]
    private $mail;

    #[ORM\Column(type: "array", nullable: false)]
    private $token;

    public function __construct(string $nickname, string $mail, array $token)
    {
        $this->nickname = $nickname;
        $this->mail = $mail;
        $this->token = $token;
    }

    public function jsonSerialize(): mixed
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
