<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Controller;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use XcoreCMS\InlineEditing\Model\ContentProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use XcoreCMS\InlineEditingBundle\Event\CheckInlinePermissionEvent;
use XcoreCMS\InlineEditingBundle\Model\EntityPersister;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class InlineEditingController
{
    /** @var ContentProvider */
    private $contentProvider;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var EntityPersister */
    private $entityPersister;

    /**
     * @param ContentProvider $contentProvider
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityPersister $entityPersister
     */
    public function __construct(
        ContentProvider $contentProvider,
        EventDispatcherInterface $eventDispatcher,
        EntityPersister $entityPersister
    ) {
        $this->contentProvider = $contentProvider;
        $this->eventDispatcher = $eventDispatcher;
        $this->entityPersister = $entityPersister;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function updateAction(Request $request): Response
    {
        /** @var CheckInlinePermissionEvent $event */
        $event = $this->eventDispatcher->dispatch(
            CheckInlinePermissionEvent::CHECK,
            new CheckInlinePermissionEvent
        );

        if ($event->isAllowed() === false) {
            return new Response('', 403);
        }

        $data = json_decode($request->getContent()) ?: [];

        foreach ($data as $item) {
            switch ($item->type ?? null) {
                case 'simple':
                    $this->contentProvider->saveContent(
                        $item->namespace ?? '',
                        $item->locale ?? '',
                        $item->name ?? '',
                        $item->content ?? ''
                    );
                    break;
                case 'entity':
                    $this->entityPersister->update(
                        $item->entity,
                        $item->id,
                        $item->property,
                        $item->content
                    );
                    break;
                default:
                    return new Response('', 400);
            }
        }

        $this->entityPersister->flush();

        return new Response;
    }
}
