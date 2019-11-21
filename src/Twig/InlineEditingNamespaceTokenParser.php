<?php

declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Twig;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class InlineEditingNamespaceTokenParser extends AbstractTokenParser
{
    /**
     * @param Token $token
     * @return InlineEditingNamespaceNode
     */
    public function parse(Token $token): InlineEditingNamespaceNode
    {
        $stream = $this->parser->getStream();

        $namespace = $stream->expect(Token::NAME_TYPE)->getValue();

        $stream->expect(Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse([$this, 'decideWithEnd'], true);

        $stream->expect(Token::BLOCK_END_TYPE);

        return new InlineEditingNamespaceNode(
            ['body' => $body],
            ['namespace' => $namespace],
            $token->getLine(),
            $this->getTag()
        );
    }

    /**
     * @param Token $token
     * @return bool
     */
    public function decideWithEnd(Token $token): bool
    {
        return $token->test(Token::NAME_TYPE, 'end_inline_namespace');
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'inline_namespace';
    }
}
