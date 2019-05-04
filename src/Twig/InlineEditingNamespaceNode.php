<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Twig;

use Twig\Compiler;
use Twig\Node\Node;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class InlineEditingNamespaceNode extends Node
{
    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler): void
    {
        $namespace = $this->getAttribute('namespace');

        $compiler
            ->write("\$context['_inline_namespace'] = '$namespace';")
            ->subcompile($this->getNode('body'))
            ->write("unset(\$context['_inline_namespace']);");
    }
}
