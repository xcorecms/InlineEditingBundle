<?php

declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Twig;

use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class InlineEditingEntityTokenParser extends AbstractTokenParser
{
    /**
     * @param Token $token
     * @return InlineEditingEntityNode<mixed>
     */
    public function parse(Token $token): InlineEditingEntityNode
    {
        $stream = $this->parser->getStream();

        $entity = $this->parser->getExpressionParser()->parseExpression();

        $stream->expect(Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse([$this, 'decideWithEnd'], true);

        $stream->expect(Token::BLOCK_END_TYPE);

        return new InlineEditingEntityNode(
            ['body' => $body, 'entity' => $entity],
            [],
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
        return $token->test(Token::NAME_TYPE, 'end_inline_entity');
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'inline_entity';
    }
}
