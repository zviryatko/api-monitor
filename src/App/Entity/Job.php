<?php
/**
 * @file
 * Contains Job
 */

namespace App\Entity;

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
     * @var \App\Entity\Profile
     */
    private $profile;

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
     * @Zend\Form\Annotation\Options({"label":"The command to run:", "class": "form-control"})
     * @var string
     */
    private $command;

    /**
     * Job constructor.
     *
     * @param string $name
     * @param int $profile
     * @param string $command
     */
    public function __construct(string $name, Profile $profile, string $command)
    {
        $this->name = $name;
        $this->profile = $profile;
        $this->command = $command;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'profile' => $this->profile->jsonSerialize(),
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
     * @return \App\Entity\Profile
     */
    public function profile(): \App\Entity\Profile
    {
        return $this->profile;
    }
}
