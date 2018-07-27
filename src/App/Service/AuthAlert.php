<?php
/**
 * @file
 * Contains App\Service\AuthAlert.
 */

namespace App\Service;


class AuthAlert implements AlertInterface
{
    /**
     * @var string
     */
    protected $error;
    protected $description;

    public function __construct(string $error, string $description)
    {
        $this->error = $error;
        $this->description = $description;
    }

    public function __toString(): string
    {
        return sprintf('Error occurred (code: <em>%s</em>): %s', $this->error, $this->description);
    }
}