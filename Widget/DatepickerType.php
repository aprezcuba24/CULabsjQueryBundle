<?php

/*
 * This file is part of the CULabsjQueryBundle package.
 *
 * (c) Alejandro Pérez Cuba <aprezcuba24@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CULabs\jQueryBundle\Widget;

use Symfony\Component\DependencyInjection\ContainerInterface;
use CULabs\jQueryBundle\Widget\PhpToJq\PhpToJqInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer;

class DatepickerType extends AbstractType
{
    protected $container, 
              $php_to_jq;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container A container.
     */
    public function __construct(ContainerInterface $container, array $php_to_jq)
    {
        $this->container = $container;        
        
        $this->php_to_jq = $php_to_jq;
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
        return 'jquery_datepicker';
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $format = $options['format'];
        $pattern = null;

        $allowedFormatOptionValues = array(
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::SHORT,
        );

        // If $format is not in the allowed options, it's considered as the pattern of the formatter if it is a string
        if (!in_array($format, $allowedFormatOptionValues, true)) {
            if (is_string($format)) {
                $defaultOptions = $this->getDefaultOptions($options);

                $format = $defaultOptions['format'];
                $pattern = $options['format'];
            } else {
                throw new CreationException('The "format" option must be one of the IntlDateFormatter constants (FULL, LONG, MEDIUM, SHORT) or a string representing a custom pattern');
            }
        }
        
        $jq_config = $options['jq_config'];
        
        if (!isset ($jq_config['dateFormat'])) {
         
            $date_formatter = new \IntlDateFormatter(\Locale::getDefault(), $format, \IntlDateFormatter::NONE, $options['user_timezone'], \IntlDateFormatter::GREGORIAN, $pattern);
                        
            $jq_config['dateFormat'] = $this->getPhpToJqService(\Locale::getDefault())->datepickerPattern($date_formatter->getPattern());            
        }
            
        $builder->appendClientTransformer(new DateTimeToLocalizedStringTransformer($options['data_timezone'], $options['user_timezone'], $format, \IntlDateFormatter::NONE, \IntlDateFormatter::GREGORIAN, $pattern));
        
        $builder
            ->setAttribute('jq_config', $jq_config)
        ;
    }
    public function buildView(FormView $view, FormInterface $form)
    {
        $this->container->get('twig.extension.form.jquery')->setTheme($view, array('CULabsjQueryBundle:Widget:datepicker.html.twig'));
        
        $view->set('jq_config', $this->joinJqConfig($form->getAttribute('jq_config')));
    }
    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'widget'         => 'choice',
            'input'          => 'datetime',            
            'format'         => \IntlDateFormatter::SHORT,
            'data_timezone'  => null,
            'user_timezone'  => null,            
            'jq_config'      => array(),
        );
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
    protected function getPhpToJqService($languaje)
    {
        $php_to_jq_service = $this->container->get($this->php_to_jq[\Locale::getDefault()]);
        
        if (!$php_to_jq_service instanceof PhpToJqInterface) {
            throw new UnexpectedTypeException($php_to_jq_service, 'CULabs\jQueryBundle\Widget\PhpToJq\PhpToJqInterface');
        }
         
        return $php_to_jq_service;
    }
}