<?php
/**
 * @file
 * Contains App\Service\Alert.
 */

namespace App\Service;

class Alert implements AlertInterface
{
    /**
     * @var string
     */
    protected $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function __toString(): string
    {
        return $this->message;
    }
}