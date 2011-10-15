<?php

namespace CULabs\jQueryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AutocompleteDoctrineController extends Controller
{    
    public function indexAction(Request $request)
    {           
        $options = $this->getOptions($request);
        
        $repository = $this->getDoctrine()->getEntityManager($options['em'])->getRepository($options['class']);
        
        $entities = call_user_func(array($repository, $options['method_for_query']), $request->query->all());
        
        $entities_json = array();
        
        foreach ($entities as $item) {
            
            $item_class = new \stdClass();
            
            $item_class->id    = call_user_func(array($item, $options['key_method']));
            $item_class->label = call_user_func(array($item, $options['method']));
            
            $entities_json[] = $item_class;
        }
        
        return new Response(json_encode($entities_json));
    }
    protected function getOptions(Request $request)
    {
        $options = array(
            'em'               => $request->get('em'),
            'class'            => $request->get('class'),
            'method_for_query' => $request->get('method_for_query'),
            'key_method'       => $request->get('key_method', 'getId'),
            'method'           => $request->get('method', '__toString'),
        );
        
        if (!isset ($options['class'])) {
            throw new \RuntimeException(sprintf('There is no `class` defined for the controller `%s` and the current route `%s`', get_class($this), $this->container->get('request')->get('_route')));
        }
        if (!isset ($options['method_for_query'])) {
            throw new \RuntimeException(sprintf('There is no `method_for_query` defined for the controller `%s` and the current route `%s`', get_class($this), $this->container->get('request')->get('_route')));
        }
        
        return $options;
    }
}