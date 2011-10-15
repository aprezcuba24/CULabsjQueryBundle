<?php

namespace CULabs\jQueryBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Pagerfanta\PagerfantaInterface;

/**
 * Description of BaseFunctionsExtension
 *
 * @author renier
 */
class BaseFunctionsExtension extends \Twig_Extension
{
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function getFunctions()
    {
        return array(
            'jq_sprintf'  => new \Twig_Function_Method($this, 'sprintf'),
        );
    }
    public function getName()
    {
        return 'jqueryreloadedbasefunction';
    }
    public function sprintf($format)
    {
        $args = func_get_args();
        
        unset ($args[0]);
        
        return vsprintf($format, $args);
    }
}
