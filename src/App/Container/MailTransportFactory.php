<?php
/**
 * @file
 * Contains App\Container\MailTransportFactory.
 */

namespace App\Container;

use Interop\Container\ContainerInterface;
use Laminas\Mail\Transport\Sendmail;
use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\ServiceManager\Factory\FactoryInterface;

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
