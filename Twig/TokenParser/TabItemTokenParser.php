<?php

namespace CULabs\jQueryBundle\Twig\TokenParser;

use CULabs\jQueryBundle\Twig\Node\TabItemNode;

/**
 * TabsTokenParser
 *
 * @author  Alejandro Pérez Cuba <aprezcuba24@gmail.com>
 */
class TabItemTokenParser extends \Twig_TokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param Twig_Token $token A Twig_Token instance
     *
     * @return Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse(\Twig_Token $token)
    {
        $lineno = $token->getLine();
        $this->parser->getStream()->expect(\Twig_Token::NAME_TYPE, 'title');
        $title = $this->parser->getExpressionParser()->parseMultitargetExpression();
        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(function (\Twig_Token $token) { 
            return $token->test(array('endjqtabitem'));
        }, true);
        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
        return new TabItemNode($body, $title, $lineno, $this->getTag());
    }
    /**
     * Gets the tag name associated with this token parser.
     *
     * @param string The tag name
     */
    public function getTag()
    {
        return 'jqtabitem';
    }
}