<?php

return [
    'dependencies' => [
        'factories' => [
            Laminas\Mail\Transport\TransportInterface::class => App\Container\MailTransportFactory::class,
        ],
    ],
];
