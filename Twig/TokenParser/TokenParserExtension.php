<?php

namespace CULabs\jQueryBundle\Twig\TokenParser;

class TokenParserExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array(
            new TabsTokenParser(),
            new TabItemTokenParser(),
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jquerytokenparser';
    }
}