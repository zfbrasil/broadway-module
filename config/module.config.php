<?php

return [
    'service_manager' => [
        'factories' => [
            Broadway\CommandHandling\SimpleCommandBus::class => ZFBrasil\BroadwayModule\Factory\SimpleCommandBusFactory::class,
        ],
    ],
];
