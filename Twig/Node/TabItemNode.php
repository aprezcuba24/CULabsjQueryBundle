<?php

namespace CULabs\jQueryBundle\Twig\Node;

/**
 * TabsTokenParser
 *
 * @author  Alejandro Pérez Cuba <aprezcuba24@gmail.com>
 */
class TabItemNode extends \Twig_Node
{
    public function __construct(\Twig_NodeInterface $body, $title, $lineno, $tag = null)
    {
        parent::__construct(array(
            'body' => $body,
            'title' => $title,
        ), array(
        ), $lineno, $tag);
    }
    public function compile(\Twig_Compiler $compiler)
    {
        $compiler->addDebugInfo($this)
                 ->write("ob_start();\n")
                 ->subcompile($this->getNode('body'))
                 ->write('$body'." = ob_get_clean();\n")
                 ->write(sprintf("\$context['%s'][] = array (\n", TabsNode::getTabName()))
                 ->indent()
                     ->write("'title' => ")
                     ->subcompile($this->getNode('title'), true)
                     ->raw(",\n")
                     ->write('\'body\' => $body,'."\n")
                 ->outdent()
                 ->write(");\n")
        ;
    }
}