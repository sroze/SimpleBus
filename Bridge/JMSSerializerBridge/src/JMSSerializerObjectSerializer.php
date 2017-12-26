<?php

declare(strict_types=1);

namespace SimpleBus\JMSSerializerBridge;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use SimpleBus\Serialization\ObjectSerializer;

class JMSSerializerObjectSerializer implements ObjectSerializer
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var string
     */
    private $format;

    public function __construct(
        SerializerInterface $serializer,
        $format
    ) {
        $this->serializer = $serializer;
        $this->format = $format;
    }

    public function serialize($object)
    {
        $serializationContext = SerializationContext::create()
            ->setSerializeNull(true);

        return $this->serializer->serialize($object, $this->format, $serializationContext);
    }

    public function deserialize($serializedObject, $type)
    {
        $deserializationContext = DeserializationContext::create()
            ->setSerializeNull(true);

        return $this->serializer->deserialize($serializedObject, $type, $this->format, $deserializationContext);
    }
}
