<?php

namespace CULabs\jQueryBundle\Twig\Node;

/**
 * DialogTokenParser
 *
 * @author  Alejandro Pérez Cuba <aprezcuba24@gmail.com>
 */
class DialogNode extends \Twig_Node
{
    public function __construct(\Twig_NodeInterface $body, $js_name, $jq_config, $lineno, $tag = null)
    {
        $id = uniqid('a');
        
        if (!$js_name) {
            $js_name = new \Twig_Node (array (
                new \Twig_Node_Expression_Constant('js_name_'.$id, $lineno),
            ));
        }
            
        parent::__construct(array(
            'body'    => $body,
            'js_name' => $js_name,
        ), array(
            'jq_config' => $jq_config,
            'id'        => $id,
        ), $lineno, $tag);
    }
    
    public function compile(\Twig_Compiler $compiler)
    {
        $id = $this->getAttribute('id');
        
        $compiler->addDebugInfo($this)
                 ->write("\$context['dialog_parent'] = (array) \$context;\n")
                 ->write("\$context['dialog_config'] = '';\n")
        ;
        
        if ($this->getAttribute('jq_config')) {            
            $compiler->write('foreach (')
                     ->subcompile($this->getAttribute('jq_config'), true)
                     ->raw(" as \$key => \$item) {\n")
                     ->indent()
                         ->write('$context[\'dialog_config\'] .= sprintf(\'%s: %s, \', $key, $item)."\n";')
                     ->outdent()
                     ->write("}\n")
                     ->write('$context[\'dialog_config\'] = substr($context[\'dialog_config\'], 0, strlen($context[\'dialog_config\']) - 2);')
            ;
        }
        
        $compiler->write(sprintf('echo "<div id=\"%s\">\n";', $id))
                 ->indent()
                     ->subcompile($this->getNode('body'))
                 ->outdent()
                 ->write('echo "</div>\n";')
        ;
        
        $compiler->write(' echo " <script type=\"text/javascript\"> \n";')
                 ->write(" echo \"  
            \".")
                 ->subcompile($this->getNode('js_name'))
                 ->raw(sprintf(".\" = $( '#%s' ).dialog({
			\".\$context['dialog_config'].\"
		});
	</script>\n\";\n", $id))
        ;
        
        $compiler->write("\$dialog_parent = \$context['dialog_parent'];\n")
                 ->write('unset($context[\'dialog_parent\'], $context[\'dialog_config\']);'."\n")
                 ->write("\$context = array_merge(\$dialog_parent, array_intersect_key(\$context, \$dialog_parent));\n")
        ;
    }
}