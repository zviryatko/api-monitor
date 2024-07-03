<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation as Form;

#[ORM\Entity]
#[ORM\Table(name: "project")]
#[Form\Name("project")]
class Project implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;

    #[ORM\ManyToOne(targetEntity: Profile::class)]
    #[ORM\JoinColumn(name: "owner", referencedColumnName: "id", nullable: false)]
    private $owner;

    #[ORM\Column(name: "name", type: "string", length: 32)]
    #[Form\Type("Laminas\Form\Element\Text")]
    #[Form\Options(["label" => "Project name (or Project name):", "class" => "form-control"])]
    private $name;

    #[ORM\Column(name: "alias", type: "string", length: 32)]
    #[Form\Type("Laminas\Form\Element\Text")]
    #[Form\Options(["label" => "Project alias:", "class" => "form-control"])]
    private $alias;

    #[ORM\ManyToMany(targetEntity: Profile::class)]
    #[ORM\JoinTable(name: "permitted_profiles")]
    #[ORM\JoinColumn(name: "project_id", referencedColumnName: "id")]
    #[ORM\InverseJoinColumn(name: "profile_id", referencedColumnName: "id", unique: true)]
    private $permittedUsers;

    #[ORM\OneToMany(targetEntity: Job::class, mappedBy: "project")]
    private $jobs;

    #[ORM\Column(name: "public", type: "boolean")]
    #[Form\Type("Laminas\Form\Element\Checkbox")]
    #[Form\Options(["label" => "Public", "class" => "form-control"])]
    private $public;

    /**
     * Project constructor.
     *
     * @param string $name
     * @param \App\Entity\Profile $owner
     * @param string $alias
     * @param bool $public
     */
    public function __construct(string $name, Profile $owner, string $alias, bool $public)
    {
        $this->name = $name;
        $this->owner = $owner;
        $this->alias = $alias;
        $this->public = $public;
        $this->permittedUsers = new ArrayCollection();
        $this->jobs = new ArrayCollection();
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'owner' => $this->getOwner()->jsonSerialize(),
            'alias' => $this->alias,
            'public' => $this->public,
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

    /**
     * @return \App\Entity\Profile
     */
    public function getOwner(): \App\Entity\Profile
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * @return Collection
     */
    public function getPermittedUsers(): Collection
    {
        return $this->permittedUsers;
    }

    /**
     * @return Collection
     */
    public function getJobs(): Collection
    {
        return $this->jobs;
    }
}
