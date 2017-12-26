<?php

declare(strict_types=1);

namespace SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Entity;

use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityAboutToBeRemoved;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityChanged;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityCreated;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityCreatedPrePersist;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityChangedPreUpdate;
use SimpleBus\DoctrineORMBridge\Tests\EventListener\Fixtures\Event\EntityNotDirty;
use SimpleBus\Message\Recorder\ContainsRecordedMessages;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class EventRecordingEntity implements ContainsRecordedMessages
{
    use PrivateMessageRecorderCapabilities;

    /**
     * @Id
     * @GeneratedValue(strategy="AUTO")
     * @Column(type="integer")
     */
    private $id;

    /**
     * @Column(type="string")
     */
    private $something;

    public function __construct()
    {
        $this->record(new EntityCreated());

        $this->something = 'initial value';
    }

    public function changeSomething(): void
    {
        $this->something = 'changed value';

        $this->record(new EntityChanged());
    }

    public function changeSomethingWithoutRecording(): void
    {
        $this->something = 'changed value';
    }

    public function prepareForRemoval(): void
    {
        $this->something = 'changed for the last time';

        $this->record(new EntityAboutToBeRemoved());
    }

    public function recordMessageWithoutStateChange(): void
    {
        $this->record(new EntityNotDirty());
    }

    /**
     * @PrePersist
     */
    public function recordMessageDuringPrePersistLifecycleCallback(): void
    {
        $this->record(new EntityCreatedPrePersist());
    }

    /**
     * @PreUpdate
     */
    public function recordMessageDuringPreUpdateLifecycleCallback(): void
    {
        $this->record(new EntityChangedPreUpdate());
    }
}
