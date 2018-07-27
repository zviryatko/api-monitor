<?php
/**
 * @file
 * Contains App\Service\AlertsInterface.
 */

namespace App\Service;

interface AlertsInterface extends \IteratorAggregate, \ArrayAccess
{
    /**
     * @param string $type
     * @param AlertInterface $message
     * @return self
     */
    public function add($type, AlertInterface $message);

    /**
     * @param AlertInterface $message
     *
     * @return self
     */
    public function addSuccess(AlertInterface $message);

    /**
     * @param AlertInterface $message
     *
     * @return self
     */
    public function addInfo(AlertInterface $message);

    /**
     * @param AlertInterface $message
     *
     * @return self
     */
    public function addWarning(AlertInterface $message);

    /**
     * @param AlertInterface $message
     *
     * @return self
     */
    public function addDanger(AlertInterface $message);
}