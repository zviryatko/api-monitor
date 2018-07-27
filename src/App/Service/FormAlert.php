<?php
/**
 * @file
 * Contains App\Service\FormAlert.
 */

namespace App\Service;

class FormAlert extends Alert implements FormAlertInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * {@inheritdoc}
     */
    public function __construct($message, $name = '')
    {
        parent::__construct($message);
        if (empty($name)) {
            throw new \InvalidArgumentException('Element name is required, for form user id');
        }
        $this->name = (string) $name;
    }

    /**
     * {@inheritdoc}
     */
    public function name()
    {
        return $this->name;
    }
}