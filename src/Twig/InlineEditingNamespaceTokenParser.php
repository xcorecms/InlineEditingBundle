<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Twig;

use Twig_Token;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class InlineEditingNamespaceTokenParser extends \Twig_TokenParser
{
    /**
     * @param Twig_Token $token
     * @return InlineEditingNamespaceNode
     */
    public function parse(Twig_Token $token): InlineEditingNamespaceNode
    {
        $stream = $this->parser->getStream();

        $namespace = $stream->expect(Twig_Token::NAME_TYPE)->getValue();

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse([$this, 'decideWithEnd'], true);

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new InlineEditingNamespaceNode(
            ['body' => $body],
            ['namespace' => $namespace],
            $token->getLine(),
            $this->getTag()
        );
    }

    /**
     * @param Twig_Token $token
     * @return bool
     */
    public function decideWithEnd(Twig_Token $token): bool
    {
        return $token->test(Twig_Token::NAME_TYPE, 'end_inline_namespace');
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'inline_namespace';
    }
}
