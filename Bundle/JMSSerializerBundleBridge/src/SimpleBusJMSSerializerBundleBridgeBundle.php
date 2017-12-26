<?php

declare(strict_types=1);

namespace SimpleBus\JMSSerializerBundleBridge;

use SimpleBus\JMSSerializerBundleBridge\DependencyInjection\SimpleBusJMSSerializerBundleBridgeExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleBusJMSSerializerBundleBridgeBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SimpleBusJMSSerializerBundleBridgeExtension();
    }
}
