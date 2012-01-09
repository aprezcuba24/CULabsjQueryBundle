<?php

namespace CULabs\jQueryBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CULabsjQueryExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);  

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        
        $this->complilePhpToJq($container);
    }
    
    protected function complilePhpToJq(ContainerBuilder $container)
    {
        $items = array();
        
        foreach ($container->findTaggedServiceIds('jquery.phptojq') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias'])
                ? $tag[0]['alias']
                : $serviceId;
            
            $items[$alias] = $serviceId;
        }
        
        $container->getDefinition('form.type.jquery.datepicker')->replaceArgument(1, $items);
        $container->getDefinition('form.type.jquery.datetimepicker')->replaceArgument(1, $items);
    }
}
