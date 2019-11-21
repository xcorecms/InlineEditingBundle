<?php

declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
abstract class AbstractInlineEnablerSubscriber implements EventSubscriberInterface
{
    /**
     * @return bool
     */
    abstract protected function isAllowedForEditation(): bool;

    /**
     * @param CheckInlinePermissionEvent $event
     */
    public function checkPermission(CheckInlinePermissionEvent $event): void
    {
        $event->setAllowed($this->isAllowedForEditation());
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [CheckInlinePermissionEvent::class => 'checkPermission'];
    }
}
