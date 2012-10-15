<?php

namespace CULabs\jQueryBundle\Widget;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ArrayChoiceList;

class AutocompleteType extends AbstractType
{
    protected $container;
    
    /**
     * Constructor.
     *
     * @param ContainerInterface $container A container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'field';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jquery_autocomplete';
    }    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['choice_list'] && !$options['choice_list'] instanceof ChoiceListInterface) {
            throw new FormException('The "choice_list" must be an instance of "Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface".');
        }
        if (!$options['choice_list']) {
            $options['choice_list'] = new ArrayChoiceList($options['choices']);
        }
        $builder->setAttribute('choice_list', $options['choice_list'])
                ->setAttribute('config', $options['config'])
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $this->container->get('twig.extension.form.jquery')->setTheme($view, array('CULabsjQueryBundle:Widget:autocomplete.html.twig'));
        
        $choices = $form->getAttribute('choice_list')->getChoices();
        
        $view->set('choices', $choices)
             ->set('id_visible', $view->get('id').'_visible')
             ->set('full_name_visible', sprintf('%s_visible[%s]', $view->getParent()->get('full_name'), $view->get('name')))
             ->set('value_hidden', $form->getClientData())
             ->set('value_visible', isset ($choices[$form->getClientData()])? $choices[$form->getClientData()]: '')
             ->set('config', $form->getAttribute('config'))
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
         return array(
             'choice_list' => null,
             'choices'     => array(),
             'config'      => '{}',
         );
    }
}