<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Controller;

use XcoreCMS\InlineEditing\Model\ContentProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use XcoreCMS\InlineEditingBundle\Event\CheckInlinePermissionEvent;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class InlineEditingController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function updateAction(Request $request): Response
    {
        /** @var CheckInlinePermissionEvent $event */
        $event = $this->get('event_dispatcher')->dispatch(
            CheckInlinePermissionEvent::CHECK,
            new CheckInlinePermissionEvent
        );

        if ($event->isAllowed() === false) {
            return new Response('', 403);
        }

        $data = json_decode($request->getContent()) ?: [];

        /** @var ContentProvider $contentProvider */
        $contentProvider = $this->get('xcore_inline.model.content_provider');

        foreach ($data as $item) {
            $contentProvider->saveContent(
                $item->namespace ?? '',
                $item->locale ?? '',
                $item->name ?? '',
                $item->content ?? ''
            );
        }

        return new Response;
    }
}
