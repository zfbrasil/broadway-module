<?php

namespace ZFBrasil\BroadwayModuleTest\Factory;

use Broadway\CommandHandling\CommandHandlerInterface;
use Broadway\CommandHandling\SimpleCommandBus;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZFBrasil\BroadwayModule\Exception\InvalidArgumentException;
use ZFBrasil\BroadwayModule\Factory\SimpleCommandBusFactory;

class SimpleCommandBusFactoryTest extends TestCase
{
    public function testSubscribesHandlersFromConfig()
    {
        $config = [
            'broadway' => [
                'command_handlers' => [
                    'Foo\Bar\Handler',
                    'Foo\Baz\Handler',
                ],
            ],
        ];

        $serviceLocator = $this->getMock(ServiceLocatorInterface::class);

        $serviceLocator
            ->expects($this->at(0))
            ->method('get')
            ->with('Config')
            ->willReturn($config);

        $barHandler = $this->getMock(CommandHandlerInterface::class);

        $serviceLocator
            ->expects($this->at(1))
            ->method('get')
            ->with('Foo\Bar\Handler')
            ->willReturn($barHandler);

        $bazHandler = $this->getMock(CommandHandlerInterface::class);

        $serviceLocator
            ->expects($this->at(2))
            ->method('get')
            ->with('Foo\Baz\Handler')
            ->willReturn($bazHandler);

        $factory    = new SimpleCommandBusFactory();
        $commandBus = $factory($serviceLocator);

        $this->assertInstanceOf(SimpleCommandBus::class, $commandBus);

        $this->assertAttributeContains($barHandler, 'commandHandlers', $commandBus);
        $this->assertAttributeContains($bazHandler, 'commandHandlers', $commandBus);
    }

    public function testThrowsExceptionIfMissingConfig()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $config = [];

        $serviceLocator = $this->getMock(ServiceLocatorInterface::class);
        $serviceLocator
            ->expects($this->once())
            ->method('get')
            ->with('Config')
            ->willReturn($config);

        $factory = new SimpleCommandBusFactory();
        $factory($serviceLocator);
    }

    public function testThrowsExceptionForInvalidCommandHandler()
    {
        $this->setExpectedException(InvalidArgumentException::class);

        $config = [
            'broadway' => [
                'command_handlers' => [
                    'Foo\Bar\ValidHandler',
                    'Foo\Baz\InvalidHandler',
                ],
            ],
        ];

        $serviceLocator = $this->getMock(ServiceLocatorInterface::class);

        $serviceLocator
            ->expects($this->at(0))
            ->method('get')
            ->with('Config')
            ->willReturn($config);

        $validHandler = $this->getMock(CommandHandlerInterface::class);

        $serviceLocator
            ->expects($this->at(1))
            ->method('get')
            ->with('Foo\Bar\ValidHandler')
            ->willReturn($validHandler);

        $invalidHandler = $this->getMock(stdClass::class);

        $serviceLocator
            ->expects($this->at(2))
            ->method('get')
            ->with('Foo\Baz\InvalidHandler')
            ->willReturn($invalidHandler);

        $factory = new SimpleCommandBusFactory();
        $factory($serviceLocator);
    }
}
