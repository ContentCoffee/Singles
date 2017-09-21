<?php

namespace Drupal\singles\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\node\NodeTypeInterface;
use Drupal\singles\Service\Singles;
use Drupal\hook_event_dispatcher\Event\Entity\BaseEntityEvent;
use Drupal\hook_event_dispatcher\HookEventDispatcherEvents;

/**
 * Class NodeTypeUpdateEventSubscriber
 * @package Drupal\singles\EventSubscriber
 */
class NodeTypeUpdateEventSubscriber implements EventSubscriberInterface
{
    /** @var singles */
    private $singles;

    /**
     * EntityEventSubscriber constructor.
     * @param singles $singles
     */
    public function __construct(
        singles $singles
    ) {
        $this->singles = $singles;
    }

    /**
     * @param BaseEntityEvent $event
     */
    public function checkForSingles(BaseEntityEvent $event)
    {
        $entity = $event->getEntity();
        if ($entity instanceof NodeTypeInterface) {
            $this->singles->checkSingle($entity);
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            HookEventDispatcherEvents::ENTITY_UPDATE => [
                ['checkForSingles'],
            ],
            HookEventDispatcherEvents::ENTITY_INSERT => [
                ['checkForSingles'],
            ],
        ];
    }
}
