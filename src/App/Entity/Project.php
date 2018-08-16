<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @Doctrine\ORM\Mapping\Entity
 * @Doctrine\ORM\Mapping\Table(name="project")
 * @Zend\Form\Annotation\Name("project")
 */
class Project implements \JsonSerializable
{
    /**
     * @Doctrine\ORM\Mapping\Id
     * @Doctrine\ORM\Mapping\Column(name="id", type="integer")
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Profile")
     * @Doctrine\ORM\Mapping\JoinColumn(name="owner", referencedColumnName="id", nullable=false)
     * @var \App\Entity\Profile
     */
    private $owner;

    /**
     * @Doctrine\ORM\Mapping\Column(name="name", type="string", length=32)
     * @Zend\Form\Annotation\Type("Zend\Form\Element\Text")
     * @Zend\Form\Annotation\Options({"label":"Project name (or Project name):", "class": "form-control"})
     * @var string
     */
    private $name;

    /**
     * @Doctrine\ORM\Mapping\Column(name="alias", type="string", length=32)
     * @Zend\Form\Annotation\Type("Zend\Form\Element\Text")
     * @Zend\Form\Annotation\Options({"label":"Project alias:", "class": "form-control"})
     * @var string
     */
    private $alias;

    /**
     * @Doctrine\ORM\Mapping\ManyToMany(targetEntity="Profile")
     * @Doctrine\ORM\Mapping\JoinTable(name="permitted_profiles",
     *      joinColumns={@Doctrine\ORM\Mapping\JoinColumn(name="project_id", referencedColumnName="id")},
     *      inverseJoinColumns={@Doctrine\ORM\Mapping\JoinColumn(name="profile_id", referencedColumnName="id", unique=true)}
     * )
     * @var Collection
     */
    private $permittedUsers;

    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="Job", mappedBy="project")
     * @var Collection
     */
    private $jobs;

    /**
     * @Doctrine\ORM\Mapping\Column(name="public", type="boolean")
     * @Zend\Form\Annotation\Type("Zend\Form\Element\Checkbox")
     * @Zend\Form\Annotation\Options({"label":"Public", "class": "form-control"})
     * @var bool
     */
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

    public function jsonSerialize()
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
