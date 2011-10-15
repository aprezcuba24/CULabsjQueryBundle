<?php

namespace CULabs\jQueryBundle\lib;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Description of Controller
 *
 * @author 
 */
class Controller extends BaseController
{
    public function rediretJs($url)
    {
        return new Response(sprintf('<script> window.location = "%s"; </script>', $url));
    }
}