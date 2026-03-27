<?php

declare(strict_types=1);

namespace Inuar\Bundle\AttributeGroupReadOnlyBundle\Infrastructure\Subscriber;

use Inuar\Bundle\AttributeGroupReadOnlyBundle\Infrastructure\Persistence\SaveReadOnlyAttributeGroupStatus;
use Akeneo\Pim\Structure\Component\Model\AttributeGroupInterface;
use Akeneo\Tool\Component\StorageUtils\StorageEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class CleanupReadOnlyAttributeGroupSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly SaveReadOnlyAttributeGroupStatus $saveStatus)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            StorageEvents::POST_REMOVE => 'onAttributeGroupRemoved',
        ];
    }

    public function onAttributeGroupRemoved(GenericEvent $event): void
    {
        $subject = $event->getSubject();
        if (!$subject instanceof AttributeGroupInterface) {
            return;
        }

        $this->saveStatus->remove($subject->getCode());
    }
}
