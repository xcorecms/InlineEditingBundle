<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Twig;

use Twig\Compiler;
use Twig\Node\Node;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class InlineEditingEntityNode extends Node
{
    /**
     * @param Compiler $compiler
     */
    public function compile(Compiler $compiler): void
    {
        $compiler
            ->write("\$context['_inline_entity'] = ")
            ->subcompile($this->getNode('entity'))
            ->raw(';')
            ->subcompile($this->getNode('body'))
            ->write("unset(\$context['_inline_entity']);");
    }
}
