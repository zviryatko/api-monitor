<?php
/**
 * @file
 * Contains CommandBase
 */

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManager;

abstract class CommandBase extends Command {
  protected $storage;

  /**
   * Constructor
   */
  public function __construct(EntityManager $storage) {
    parent::__construct();
    $this->storage = $storage;
  }
}
