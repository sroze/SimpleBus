<?php

declare(strict_types=1);

namespace SimpleBus\SymfonyBridge;

use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\AddMiddlewareTags;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\AutoRegister;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\CompilerPassUtil;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\ConfigureMiddlewares;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterMessageRecorders;
use SimpleBus\SymfonyBridge\DependencyInjection\Compiler\RegisterSubscribers;
use SimpleBus\SymfonyBridge\DependencyInjection\EventBusExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusEventBusBundle extends Bundle
{
    use RequiresOtherBundles;

    private $configurationAlias;

    public function __construct($alias = 'event_bus')
    {
        $this->configurationAlias = $alias;
    }

    public function build(ContainerBuilder $container): void
    {
        $this->checkRequirements(['SimpleBusCommandBusBundle'], $container);

        $container->addCompilerPass(
            new AutoRegister('event_subscriber', 'subscribes_to'),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            10
        );

        $container->addCompilerPass(
            new ConfigureMiddlewares(
                'event_bus',
                'event_bus_middleware'
            )
        );

        $container->addCompilerPass(
            new RegisterMessageRecorders(
                'simple_bus.event_bus.aggregates_recorded_messages',
                'event_recorder'
            )
        );

        $container->addCompilerPass(
            new RegisterSubscribers(
                'simple_bus.event_bus.event_subscribers_collection',
                'event_subscriber',
                'subscribes_to'
            )
        );

        CompilerPassUtil::prependBeforeOptimizationPass(
            $container,
            new AddMiddlewareTags(
                'simple_bus.event_bus.handles_recorded_messages_middleware',
                ['command'],
                200
            )
        );

        // @TODO Fix unit tests
        // LogicException: Tag "message_bus" of service "simple_bus.asynchronous.command_bus" should have an attribute "bus_name"
        //
        //if (!$container->hasExtension('simplebus_profiler')) {
        //    $container->addCompilerPass(
        //        new DependencyInjection\Compiler\ProfilerPass()
        //    );
        //
        //    $container->registerExtension(new DependencyInjection\ProfilerExtension());
        //}
    }

    public function getContainerExtension()
    {
        return new EventBusExtension($this->configurationAlias);
    }
}
