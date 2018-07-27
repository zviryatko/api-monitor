<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="log")
 */
class Log implements \JsonSerializable
{
  /**
   * @ORM\Id
   * @ORM\Column(name="id", type="integer")
   * @ORM\GeneratedValue(strategy="IDENTITY")
   * @var int
   */
    private $id;

  /**
   * @ORM\ManyToOne(targetEntity="Job")
   * @ORM\JoinColumn(name="job_id", referencedColumnName="id")
   * @var \App\Entity\Job
   */
    private $job;

  /**
   * @ORM\Column(name="up", type="boolean")
   * @var bool
   */
    private $up;

  /**
   * @ORM\Column(name="created", type="datetime")
   * @var \DateTime
   */
    private $created;

  /**
   * Application constructor.
   * @param $name
   * @param $up
   * @param $created
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
