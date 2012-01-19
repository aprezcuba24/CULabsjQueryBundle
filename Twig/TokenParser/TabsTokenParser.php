<?php

namespace CULabs\jQueryBundle\Twig\TokenParser;

use CULabs\jQueryBundle\Twig\Node\TabsNode;

/**
 * TabsTokenParser
 *
 * @author  Alejandro Pérez Cuba <aprezcuba24@gmail.com>
 */
class TabsTokenParser extends \Twig_TokenParser
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
        
        $jq_config = null;
        if ($this->parser->getStream()->test(\Twig_Token::NAME_TYPE, 'config')) {
            $this->parser->getStream()->next();

            $jq_config = $this->parser->getExpressionParser()->parseExpression();
        }
        $js_name = null;
        if ($this->parser->getStream()->test(\Twig_Token::NAME_TYPE, 'js_name')) {
            $this->parser->getStream()->next();

            $js_name = $this->parser->getExpressionParser()->parseMultitargetExpression();
        }
        
        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
        
        $body = $this->parser->subparse(function (\Twig_Token $token) { 
            return $token->test(array('endjqtabs'));
        }, true);
        
        $this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
        return new TabsNode($body, $js_name, $jq_config, $lineno, $this->getTag());
    }
    /**
     * Gets the tag name associated with this token parser.
     *
     * @param string The tag name
     */
    public function getTag()
    {
        return 'jqtabs';
    }
}