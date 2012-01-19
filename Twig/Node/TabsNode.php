<?php

namespace CULabs\jQueryBundle\Twig\Node;

/**
 * TabsTokenParser
 *
 * @author  Alejandro Pérez Cuba <aprezcuba24@gmail.com>
 */
class TabsNode extends \Twig_Node
{
    static protected $indent = array();
    
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
    public static function getTabName()
    {
        end(self::$indent);
        return current(self::$indent);
    }
    public function compile(\Twig_Compiler $compiler)
    {
        $id = $this->getAttribute('id');
        array_push(self::$indent, 'tabview_'.$id);
        
        $compiler->addDebugInfo($this)
                 ->write("\$context['tabs_parent'] = (array) \$context;\n")
                 ->write("\$context['tabs_config'] = '';\n");
             
        if ($this->getAttribute('jq_config')) {            
            $compiler->write('foreach (')
                     ->subcompile($this->getAttribute('jq_config'), true)
                     ->raw(" as \$key => \$item) {\n")
                     ->indent()
                         ->write('$context[\'tabs_config\'] .= sprintf(\'%s: %s, \', $key, $item)."\n";')
                     ->outdent()
                     ->write("}\n")
                     ->write('$context[\'tabs_config\'] = substr($context[\'tabs_config\'], 0, strlen($context[\'tabs_config\']) - 2);')
            ;
        }
        
        $compiler->write(sprintf('echo "<div id =\'%s\'>\n";'."\n", $id))
                 ->indent()
                     ->write(sprintf("\$context['%s'] = array();", TabsNode::getTabName()))
                     ->subcompile($this->getNode('body'))
                     ->write("echo \"<ul>\n\";\n")
                     ->write(sprintf("foreach(\$context['%s'] as \$key => \$item) {\n", self::getTabName()))
                     ->indent()
                         ->write("echo sprintf('<li><a href=\"#tabs-%s\">%s</a></li>', \$key, \$item['title']).\"\n\";\n")
                     ->outdent()
                     ->write("}\n")
                     ->write("echo \"</ul>\n\";\n")
                     ->write(sprintf("foreach(\$context['%s'] as \$key => \$item) {\n", self::getTabName()))
                     ->indent()
                         ->write("echo sprintf('<div id=\"tabs-%s\">%s</div>', \$key, \$item['body']).\"\n\";\n")
                     ->outdent()
                     ->write("}\n")
                 ->outdent()
                 ->write(' echo "</div>";');
        
        $compiler->write(' echo " <script type=\"text/javascript\"> \n";')
                 ->write(" echo \"  
            \".")
                 ->subcompile($this->getNode('js_name'))
                 ->raw(sprintf(".\" = $( '#%s' ).tabs({
			\".\$context['tabs_config'].\"
		});
	</script>\n\";\n", $id));
        
        $compiler->write("\$tabs_parent = \$context['tabs_parent'];\n")
                 ->write('unset($context[\''.self::getTabName().'\'], $context[\'tabs_parent\'], $context[\'tabs_config\']);'."\n")
                 ->write("\$context = array_merge(\$tabs_parent, array_intersect_key(\$context, \$tabs_parent));\n");
        
         array_pop(self::$indent);       
    }
}