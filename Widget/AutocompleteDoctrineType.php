<?php

namespace CULabs\jQueryBundle\Widget;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\DataTransformerInterface;

class AutocompleteDoctrineType extends AbstractType implements DataTransformerInterface
{
    protected $container, 
              $registry,
              $options;
    
    public function __construct(ContainerInterface $container, RegistryInterface $registry)
    {
        $this->container = $container;
        $this->registry  = $registry;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        if (!$options['url']) {
            throw new FormException('The "url" option no must be null');
        }
        if (!$options['class']) {
            throw new FormException('The "class" option no must be null');
        }
        
        $this->options = $options;
        
        $builder->setAttribute('url', $options['url'])
                ->setAttribute('config', $options['config'])
                ->setAttribute('limit', $options['limit'])
                ->setAttribute('key_method', $options['key_method'])
                ->setAttribute('em', $options['em'])
                ->setAttribute('class', $options['class'])
                ->setAttribute('method_for_query', $options['method_for_query'])
                ->setAttribute('method', $options['method'])
        ;
        $builder->prependClientTransformer($this);
    }
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form)
    {
        $this->container->get('twig.extension.form.jquery')->setTheme($view, array('CULabsjQueryBundle:Widget:doctrine_autocomplete.html.twig'));
        
        $key = $this->transform($form->getData());
        
        $view->set('id_visible', $view->get('id').'_visible')
             ->set('full_name_visible', sprintf('%s_visible[%s]', $view->getParent()->get('full_name'), $view->get('name')))
             ->set('value_hidden', $key)
             ->set('value_visible', $this->getVisibleValue($key))
             ->set('config', $form->getAttribute('config'))
             ->set('url', $form->getAttribute('url'))
             ->set('limit', $form->getAttribute('limit'))
        ;
    }
    protected function getVisibleValue($value)
    {
        if (!$value)
            return '';
        
        return call_user_func(array($this->reverseTransform($value), $this->options['method']));
    }
    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'em'               => null,
            'class'            => null,
            'url'              => null,
            'method_for_query' => 'findOneById',
            'method'           => '__toString',
            'key_method'       => 'getId',
            'config'           => '{}',
            'limit'            => 10
        );

        $options = array_merge($defaultOptions, $options);
        
        $options['em'] = $this->registry->getEntityManager($options['em']);
        
        return $options;
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
        return 'jquery_doctrine_autocomplete';
    }
    /**
     * {@inheritdoc}
     */
    function transform($entity)
    {
        if (null === $entity || '' === $entity) {
            return '';
        }

        if (!is_object($entity)) {
            throw new UnexpectedTypeException($entity, 'object');
        }
        
        return call_user_func(array($entity, $this->options['key_method']));
    }
    function reverseTransform($key)
    {        
        if ('' === $key || null === $key) {
            return null;
        }

        if (!is_numeric($key)) {
            throw new UnexpectedTypeException($key, 'numeric');
        }
        
        $repository = $this->options['em']->getRepository($this->options['class']);

        if (!($entity = call_user_func(array($repository, $this->options['method_for_query']), $key))) {
            throw new TransformationFailedException(sprintf('The entity with key "%s" could not be found', $key));
        }

        return $entity;
    }
}