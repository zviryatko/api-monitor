<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Process\Process;
use Doctrine\ORM\Mapping as ORM;
use Laminas\Form\Annotation as Form;

#[ORM\Entity]
#[ORM\Table(name: "job")]
#[Form\Name("job")]
class Job implements \JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "integer")]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;

    #[ORM\ManyToOne(targetEntity: Profile::class)]
    #[ORM\JoinColumn(name: "profile", referencedColumnName: "id", nullable: false)]
    private $profile;

    #[ORM\ManyToOne(targetEntity: Project::class, inversedBy: "jobs")]
    #[ORM\JoinColumn(name: "project", referencedColumnName: "id", nullable: false)]
    private $project;

    #[ORM\Column(name: "name", type: "string", length: 32, nullable: false)]
    #[Form\Type("Laminas\Form\Element\Text")]
    #[Form\Options(["label" => "Job name:", "class" => "form-control"])]
    private $name;

    #[ORM\Column(name: "command", type: "text", nullable: false)]
    #[Form\Type("Laminas\Form\Element\Textarea")]
    #[Form\Options(["label" => "The command's project:", "class" => "form-control"])]
    private $command;

    #[ORM\OneToMany(targetEntity: Log::class, mappedBy: "job")]
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

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'profile' => $this->profile->jsonSerialize(),
            'project' => $this->project->jsonSerialize(),
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
        $process = new Process([$this->getCommand()]);
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

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }
}
