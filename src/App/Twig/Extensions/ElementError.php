<?php
/**
 * @file
 * Contains App\Twig\Extensions\ElementError.
 */

namespace App\Twig\Extensions;

use App\Service\Alerts;
use App\Service\AlertsInterface;
use App\Service\FormAlertInterface;
use Interop\Container\ContainerInterface;

class ElementError extends \Twig_Extension
{
    /**
     * @var \App\Service\AlertsInterface
     */
    protected $alerts;

    /**
     * @param \App\Service\AlertsInterface $alerts
     */
    public function __construct(AlertsInterface $alerts)
    {
        $this->alerts = $alerts;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'element_error';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('element_error_class', [$this, 'elementErrorClass']),
        ];
    }

    public function elementErrorClass($name, $class = ' has-error')
    {
        $errors = (array) array_merge($this->alerts[Alerts::DANGER] ?: [], $this->alerts[Alerts::WARNING] ?: []);
        foreach ($errors as $error) {
            if ($error instanceof FormAlertInterface && $error->name() === (string) $name) {
                return $class;
            }
        }

        return '';
    }

    public static function factory(ContainerInterface $container)
    {
        return new self($container->get(AlertsInterface::class));
    }
}