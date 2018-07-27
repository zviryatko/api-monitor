<?php

return [
    'dependencies' => [
        'factories' => [
            Zend\Mail\Transport\TransportInterface::class => App\Container\MailTransportFactory::class,
        ],
    ],
];