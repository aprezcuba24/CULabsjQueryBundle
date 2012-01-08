<?php

namespace CULabs\jQueryBundle\Widget\PhpToJq;

class PhpToJqEs implements PhpToJqInterface
{
    public function datepickerPattern($pattern)
    {
        $pattern_php = array(
            'dd/MM/yyyy'                  => "'d/mm/yy'",
            'EEEE d \'de\' MMMM \'de\' y' => "'DD d \'de\' MM \'de\' yy'",
            'd \'de\' MMMM \'de\' y'      => "'d \'de\' MM \'de\' yy'",
            'dd/MM/yy'                    => "'dd/mm/y'",
        );
        
        return isset ($pattern_php[$pattern])? $pattern_php[$pattern]: "'mm/dd/yy'";
    }
}