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
     * @var array messages projected by type.
     */
    protected $messages = [];

    /**
     * {@inheritdoc}
     */
    public function add($type, AlertInterface $message): AlertsInterface|static
    {
        $this->messages[$type][] = $message;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addSuccess(AlertInterface $message): AlertsInterface|static
    {
        return $this->add(self::SUCCESS, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function addInfo(AlertInterface $message): AlertsInterface|static
    {
        return $this->add(self::INFO, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function addWarning(AlertInterface $message): AlertsInterface|static
    {
        return $this->add(self::WARNING, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function addDanger(AlertInterface $message): AlertsInterface|static
    {
        return $this->add(self::DANGER, $message);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): \Traversable
    {
        return new \RecursiveArrayIterator($this->messages);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->messages[$offset]) && count($this->messages[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset): mixed
    {
        if ($this->offsetExists($offset)) {
            return $this->messages[$offset];
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        throw new \BadMethodCallException('Use add() method instead.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->messages[$offset]);
    }
}
