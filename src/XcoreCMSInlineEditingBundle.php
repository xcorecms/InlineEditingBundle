<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class XcoreCMSInlineEditingBundle extends Bundle
{
    /**
     * @return null|\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getContainerExtension()
    {
        if ($this->extension === null) {
            $this->extension = $this->createContainerExtension();
        }

        return $this->extension;
    }
}
