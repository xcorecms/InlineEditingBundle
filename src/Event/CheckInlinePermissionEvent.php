<?php

declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class CheckInlinePermissionEvent extends Event
{
    /**
     * @var bool
     */
    private $editationAllowed;

    /**
     * @param bool $editationAllowed
     */
    public function __construct(bool $editationAllowed = false)
    {
        $this->editationAllowed = $editationAllowed;
    }

    /**
     * @param bool $status
     */
    public function setAllowed(bool $status = true): void
    {
        $this->editationAllowed = $status;
    }

    /**
     * @return bool
     */
    public function isAllowed(): bool
    {
        return $this->editationAllowed;
    }
}
