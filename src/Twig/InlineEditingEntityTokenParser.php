<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\Twig;

use Twig_Token;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class InlineEditingEntityTokenParser extends \Twig_TokenParser
{
    /**
     * @param Twig_Token $token
     * @return InlineEditingEntityNode
     */
    public function parse(Twig_Token $token): InlineEditingEntityNode
    {
        $stream = $this->parser->getStream();

        $entity = $this->parser->getExpressionParser()->parseExpression();

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        $body = $this->parser->subparse([$this, 'decideWithEnd'], true);

        $stream->expect(Twig_Token::BLOCK_END_TYPE);

        return new InlineEditingEntityNode(
            ['body' => $body, 'entity' => $entity],
            [],
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
        return $token->test(Twig_Token::NAME_TYPE, 'end_inline_entity');
    }

    /**
     * @return string
     */
    public function getTag(): string
    {
        return 'inline_entity';
    }
}
