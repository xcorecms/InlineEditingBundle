<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Controller;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use XcoreCMS\InlineEditing\Model\Entity\EntityPersister;
use XcoreCMS\InlineEditing\Model\Entity\HtmlEntityElement\Element;
use XcoreCMS\InlineEditing\Model\Simple\ContentProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use XcoreCMS\InlineEditingBundle\Event\CheckInlinePermissionEvent;

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
        $event = $this->eventDispatcher->dispatch(new CheckInlinePermissionEvent());

        if ($event->isAllowed() === false) {
            return new JsonResponse([], 403);
        }

        $data = json_decode((string) $request->getContent(), true) ?: [];

        $payload = [];

        foreach ($data as $elementId => $item) {
            $type = $item['type'] ?? null;
            if ($type === 'simple') {
                $this->processSimple($item);
                $payload[$elementId] = ['status' => 0];
            } elseif ($type === 'entity' || $type === 'entity-specific') {
                $this->processEntity($item);
            }
        }

        $container = $this->entityPersister->flush();
        $payload = array_merge($payload, $container->generateResponse());

        return new JsonResponse($payload, $container->isValid() === true ? 200 : 400);
    }


    /**
     * @param array $item
     */
    protected function processSimple(array $item): void
    {
        if (!isset($item['namespace'], $item['locale'], $item['name'], $item['content'])) {
            return;
        }

        $this->contentProvider->saveContent($item['namespace'], $item['locale'], $item['name'], $item['content']);
    }

    /**
     * @param array $item
     */
    protected function processEntity(array $item): void
    {
        if (!isset($item['entity'], $item['id'], $item['property'], $item['content'])) {
            return;
        }

        $this->entityPersister->update(new Element($item['entity'], $item['id'], $item['property'], $item['content']));
    }
}
