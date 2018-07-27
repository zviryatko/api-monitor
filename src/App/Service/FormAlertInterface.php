<?php
/**
 * @file
 * Contains App\Service\FormAlertInterface.
 */

namespace App\Service;

interface FormAlertInterface extends AlertInterface
{
    /**
     * @param $message
     * @param $name
     */
    public function __construct($message, $name = '');

    /**
     * @return string
     */
    public function name();
}