<?php

namespace ZFBrasil\BroadwayModule\Factory;

use Broadway\CommandHandling\CommandHandlerInterface;
use Broadway\CommandHandling\SimpleCommandBus;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZFBrasil\BroadwayModule\Exception\InvalidArgumentException;

final class SimpleCommandBusFactory
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return SimpleCommandBus
     */
    public function __invoke(ServiceLocatorInterface $serviceLocator)
    {
        $commandBus = new SimpleCommandBus();
        $config     = $serviceLocator->get('Config');

        if (! isset($config['broadway']['command_handlers'])) {
            throw new InvalidArgumentException('Missing command handlers config');
        }

        $handlerList = $config['broadway']['command_handlers'];

        foreach ($handlerList as $handlerName) {
            /* @var CommandHandlerInterface $handler */
            $handler = $serviceLocator->get($handlerName);

            if (! $handler instanceof CommandHandlerInterface) {
                throw new InvalidArgumentException(sprintf(
                    'Command handler must be an instance of %s, %s given.',
                    CommandHandlerInterface::class,
                    is_object($handler) ? get_class($handler) : gettype($handler)
                ));
            }

            $commandBus->subscribe($handler);
        }

        return $commandBus;
    }
}
