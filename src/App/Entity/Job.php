<?php
/**
 * @file
 * Contains Job
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use Symfony\Component\Process\Process;

/**
 * @ORM\Entity
 * @ORM\Table(name="job")
 * @Annotation\Name("job")
 */
class Job  implements \JsonSerializable
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
   * @Annotation\Options({"label":"Job name:", "class": "form-control"})
   * @var string
   */
  private $name;

  /**
   * @ORM\Column(name="command", type="text")
   * @Annotation\Type("Zend\Form\Element\Textarea")
   * @Annotation\Options({"label":"The command to run:", "class": "form-control"})
   * @var string
   */
  private $command;

  /**
   * Application constructor.
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
      'command' => $this->command,
    ];
  }

  /**
   * @return int
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName(): string {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getCommand(): string {
    return $this->command;
  }

  /**
   * Execute job.
   *
   * @return bool
   */
  public function execute() {
    $process = new Process($this->getCommand());
    $process->run();
    return $process->isSuccessful();
  }
}
