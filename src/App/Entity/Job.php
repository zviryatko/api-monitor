<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Process\Process;

/**
 * @Doctrine\ORM\Mapping\Entity
 * @Doctrine\ORM\Mapping\Table(name="job")
 * @Zend\Form\Annotation\Name("job")
 */
class Job implements \JsonSerializable
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
     * @Doctrine\ORM\Mapping\JoinColumn(name="profile", referencedColumnName="id", nullable=false)
     * @var Profile
     */
    private $profile;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Project", inversedBy="jobs")
     * @Doctrine\ORM\Mapping\JoinColumn(name="project", referencedColumnName="id", nullable=false)
     * @var Project
     */
    private $project;

    /**
     * @Doctrine\ORM\Mapping\Column(name="name", type="string", length=32, nullable=false)
     * @Zend\Form\Annotation\Type("Zend\Form\Element\Text")
     * @Zend\Form\Annotation\Options({"label":"Job name:", "class": "form-control"})
     * @var string
     */
    private $name;

    /**
     * @Doctrine\ORM\Mapping\Column(name="command", type="text", nullable=false)
     * @Zend\Form\Annotation\Type("Zend\Form\Element\Textarea")
     * @Zend\Form\Annotation\Options({"label":"The command's project:", "class": "form-control"})
     * @var string
     */
    private $command;

    /**
     * Bidirectional - One-To-Many (INVERSE SIDE)
     *
     * @Doctrine\ORM\Mapping\OneToMany(targetEntity="Log", mappedBy="job")
     * @var Collection
     */
    private $logs;

    /**
     * Job constructor.
     *
     * @param string $name
     * @param Profile $profile
     * @param Project $project
     * @param string $command
     */
    public function __construct(string $name, Profile $profile, Project $project, string $command)
    {
        $this->name = $name;
        $this->profile = $profile;
        $this->project = $project;
        $this->command = $command;
        $this->logs = new ArrayCollection();
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'profile' => $this->profile->jsonSerialize(),
            'project' => $this->getProject()->jsonSerialize(),
            'name' => $this->name,
            'command' => $this->command,
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
     * @return string
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Execute job.
     *
     * @return bool
     */
    public function execute()
    {
        $process = new Process($this->getCommand());
        $process->run();
        return $process->isSuccessful();
    }

    /**
     * @return Profile
     */
    public function profile(): Profile
    {
        return $this->profile;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @return Collection
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }
}
