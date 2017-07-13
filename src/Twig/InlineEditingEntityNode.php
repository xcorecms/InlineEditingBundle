<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Twig;

use Twig_Node;
use Twig_Compiler;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class InlineEditingEntityNode extends Twig_Node
{
    /**
     * @param Twig_Compiler $compiler
     */
    public function compile(Twig_Compiler $compiler): void
    {
        $compiler
            ->write("\$context['_inline_entity'] = ")
            ->subcompile($this->getNode('entity'))
            ->raw(';')
            ->subcompile($this->getNode('body'))
            ->write("unset(\$context['_inline_entity']);");
    }
}
