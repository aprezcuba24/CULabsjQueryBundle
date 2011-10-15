<?php

namespace CULabs\jQueryBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Pagerfanta\PagerfantaInterface;

/**
 * Description of AjaxExtension
 *
 * @author 
 */
class AjaxExtension extends \Twig_Extension
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
            'jq_remote_function' => new \Twig_Function_Method($this, 'jqRemoteFunction', array('is_safe' => array('html'))),
            'jq_remote_form'     => new \Twig_Function_Method($this, 'jqRemoteForm', array('is_safe' => array('html'))),
        );
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jqueryreloaded';
    }
    protected function javascript_tag($code)
    {
        return sprintf('
            <script type="text/javascript">
            //<![CDATA[
            %s
            //]]>
            </script>
            ', $code);
    }
    public function jqRemoteFunction(array $options)
    {
        // Defining elements to update
	if (isset($options['update']) && is_array($options['update']))
	{
		// On success, update the element with returned data
		if (isset($options['update']['success'])) $update_success = "#".$options['update']['success'];

		// On failure, execute a client-side function
		if (isset($options['update']['failure'])) $update_failure = $options['update']['failure'];
	}
	else if (isset($options['update'])) $update_success = "#".$options['update'];
        
        // Update method
	$updateMethod = $this->updateMethod(isset($options['position']) ? $options['position'] : '');
        
        // Callbacks
	if (isset($options['loading'])) $callback_loading = $options['loading'];
	if (isset($options['complete'])) $callback_complete = $options['complete'];
	if (isset($options['success'])) $callback_success = $options['success'];

	$execute = 'false';
	if ((isset($options['script'])) && ($options['script'] == '1')) $execute = 'true';

	// Data Type
	if (isset($options['dataType']))
	{
		$dataType = $options['dataType'];
	}
	elseif ($execute)
	{
		$dataType = 'html';
	}
	else
	{
		$dataType = 'text';
	}
        // POST or GET ?
	$method = 'POST';
	if ((isset($options['method'])) && (strtoupper($options['method']) == 'GET')) $method = $options['method'];
        
        // async or sync, async is default
	if ((isset($options['type'])) && ($options['type'] == 'synchronous')) $type = 'false';
        
        if (isset($options['with'])) $formData = $options['with'];
        
        // build the function
	$function = isset ($options['function_name'])? $options['function_name']: "jQuery.ajax({";
	$function .= 'type:\''.$method.'\'';
	$function .= ',dataType:\'' . $dataType . '\'';
	if (isset($type)) $function .= ',async:'.$type;
	if (isset($formData)) $function .= ',data:'.$formData;
	if (isset($update_success) and !isset($callback_success)) $function .= ',success:function(data, textStatus){jQuery(\''.$update_success.'\').'.$updateMethod.'(data);}';
	if (isset($update_failure)) $function .= ',error:function(XMLHttpRequest, textStatus, errorThrown){'.$update_failure.'}';
	if (isset($callback_loading)) $function .= ',beforeSend:function(XMLHttpRequest){'.$callback_loading.'}';
	if (isset($callback_complete)) $function .= ',complete:function(XMLHttpRequest, textStatus){'.$callback_complete.'}';
	if (isset($callback_success)) $function .= ',success:function(data, textStatus){'.$callback_success.'}';
	$function .= ',url:\''.$options['url'].'\'';
	$function .= '})';
        
        if (isset($options['before']))
	{
		$function = $options['before'].'; '.$function;
	}
	if (isset($options['after']))
	{
		$function = $function.'; '.$options['after'];
	}
	if (isset($options['condition']))
	{
		$function = 'if ('.$options['condition'].') { '.$function.'; }';
	}
	if (isset($options['confirm']))
	{
		$function = "if (confirm('".escape_javascript($options['confirm'])."')) { $function; }";
		if (isset($options['cancel']))
		{
			$function = $function.' else { '.$options['cancel'].' }';
		}
	}

	return $function;
    }
    public function jqRemoteForm($form_id, array $options)
    {
        return $this->javascript_tag(sprintf("$(document).ready(function() {%s});", $this->jqRemoteFunction(array_merge($options, array(
            'function_name' => sprintf("$('#%s').ajaxForm({", $form_id)
        )))));
    }
    protected function updateMethod($position) {
	// Updating method
	$updateMethod = 'html';
	switch ($position) {
		case 'before':$updateMethod='before';break;
		case 'after':$updateMethod='after';break;
		case 'top':$updateMethod='prepend';break;
		case 'bottom':$updateMethod='append';break;
	}

	return $updateMethod;
    }
}
