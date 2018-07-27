<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

/**
 * @ORM\Entity
 * @ORM\Table(name="group")
 * @Annotation\Name("group")
 */
class Group implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=32)
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Options({"label":"Group name:", "class": "form-control"})
     * @var string
     */
    private $name;

    /**
     * Application constructor.
     *
     * @param $name
     * @param $command
     */
    public function __construct(string $name, string $command)
    {
        $this->name = $name;
        $this->command = $command;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

}
