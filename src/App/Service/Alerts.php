<?php
/**
 * @file
 * Contains App\Service\Alerts.
 */

namespace App\Service;

class Alerts implements AlertsInterface
{
    const SUCCESS = 'success';
    const INFO = 'info';
    const WARNING = 'warning';
    const DANGER = 'danger';

    /**
     * @var array messages grouped by type.
     */
    protected $messages = [];

    /**
     * {@inheritdoc}
     */
    public function add($type, AlertInterface $message)
    {
        $this->messages[$type][] = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addSuccess(AlertInterface $message)
    {
        return $this->add(self::SUCCESS, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function addInfo(AlertInterface $message)
    {
        return $this->add(self::INFO, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function addWarning(AlertInterface $message)
    {
        return $this->add(self::WARNING, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function addDanger(AlertInterface $message)
    {
        return $this->add(self::DANGER, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \RecursiveArrayIterator($this->messages);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->messages[$offset]) && count($this->messages[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->messages[$offset];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Use add() method instead.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->messages[$offset]);
    }
}