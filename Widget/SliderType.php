<?php

namespace CULabs\jQueryBundle\Widget;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilder;

class SliderType extends IntegerType
{
    protected $container;

    /**
     * Constructor.
     * @param ContainerInterface $container A container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);
        
        $builder
            ->setAttribute('jq_config', $options['jq_config'])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $this->container->get('twig.extension.form.jquery')->setTheme($view, array('CULabsjQueryBundle:Widget:slider.html.twig'));
        
        parent::buildView($view, $form);
        
        $jq_config = $form->getAttribute('jq_config');
        $jq_config['value'] = isset ($jq_config['value'])? $jq_config['value']: $view->get('value');
        $jq_config['range'] = isset ($jq_config['range'])? $jq_config['range']: '"min"';
        if (isset ($jq_config['slide'])) {
            $slide = trim($jq_config['slide']);
            $slide = rtrim($slide, '}');
            $slide .= sprintf("
                $( '#%s' ).val( ui.value );
                $( '#%s_indicator' ).text( ui.value );
            }", $view->get('id'), $view->get('id'));
            $jq_config['slide'] = $slide;
        } else {
            $jq_config['slide'] = sprintf("function ( event, ui ) {
                $( '#%s' ).val( ui.value );
                $( '#%s_indicator' ).text( ui.value );
            }", $view->get('id'), $view->get('id'));            
        }
        
        $view->set('jq_config', $this->joinJqConfig($jq_config));
    }
    
    protected function joinJqConfig($jq_config)
    {
        $config = '';
        
        foreach ($jq_config as $key => $item) {
            
            $config .= sprintf('%s: %s, ', $key, $item);
        }
        
        $config = substr($config, 0, strlen($config) - 2);
        
        return $config;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getParent(array $options)
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jquery_slider';
    }
    public function getDefaultOptions(array $options)
    {
        return array_merge(parent::getDefaultOptions($options), array(            
            'jq_config' => array(),
        ));
    }
}