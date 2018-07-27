<?php

namespace App\Entity;

/**
 * @Doctrine\ORM\Mapping\Entity
 * @Doctrine\ORM\Mapping\Table(name="log")
 */
class Log implements \JsonSerializable
{

    /**
     * @Doctrine\ORM\Mapping\Id
     * @Doctrine\ORM\Mapping\Column(name="id", type="integer")
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    private $id;

    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="Job")
     * @Doctrine\ORM\Mapping\JoinColumn(name="job_id", referencedColumnName="id", nullable=false)
     * @var \App\Entity\Job
     */
    private $job;

    /**
     * @Doctrine\ORM\Mapping\Column(name="up", type="boolean", nullable=false)
     * @var bool
     */
    private $up;

    /**
     * @Doctrine\ORM\Mapping\Column(name="created", type="datetime", nullable=false)
     * @var \DateTime
     */
    private $created;

    /**
     * Log constructor.
     *
     * @param \App\Entity\Job $job
     * @param bool $up
     * @param \DateTime $created
     */
    public function __construct(Job $job, bool $up, \DateTime $created = null)
    {
        $this->job = $job;
        $this->up = $up;
        $this->created = $created ?? new \DateTime('now');
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'job' => $this->job->jsonSerialize(),
            'up' => $this->up,
            'created' => $this->created->getTimestamp(),
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getJob(): Job
    {
        return $this->job;
    }

    /**
     * @return bool
     */
    public function isUp(): bool
    {
        return $this->up;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }
}
