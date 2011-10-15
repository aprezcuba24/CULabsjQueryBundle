<?php

namespace CULabs\jQueryBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Pagerfanta\PagerfantaInterface;

/**
 * Description of JavascriptagExtension
 *
 * @author 
 */
class JavascripBaseExtension extends \Twig_Extension
{    
    private $container;
    
    /**
     * Constructor.
     *
     * @param ContainerInterface $container A container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function getFunctions()
    {
        return array(
            'javascript_tag'   => new \Twig_Function_Method($this, 'javascript_tag', array('is_safe' => array('html'))),
            'begin_javascript' => new \Twig_Function_Method($this, 'beginJavascipt', array('is_safe' => array('html'))),
            'end_javascript'   => new \Twig_Function_Method($this, 'endJavascript', array('is_safe' => array('html'))),
        );
    }
    public function javascript_tag($code)
    {
        return sprintf('
            <script type="text/javascript">
            //<![CDATA[
            %s
            //]]>
            </script>
            ', $code);        
    }
    public function beginJavascipt()
    {
        return '
            <script type="text/javascript">
            //<![CDATA[
            ';
    }
    public function endJavascript()
    {
        return '
            //]]>
            </script>
            ';
    }
    public function getName()
    {
        return 'javascriptbase';
    }
}