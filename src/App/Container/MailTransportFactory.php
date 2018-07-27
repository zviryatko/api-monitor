<?php
/**
 * @file
 * Contains App\Container\MailTransportFactory.
 */

namespace App\Container;

use Interop\Container\ContainerInterface;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\ServiceManager\Factory\FactoryInterface;

class MailTransportFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (empty($container->get('config')['smtp'])) {
            return new Sendmail();
        }
        return new Smtp(new SmtpOptions($container->get('config')['smtp']));
    }
}
