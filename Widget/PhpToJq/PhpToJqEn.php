<?php

namespace CULabs\jQueryBundle\Widget\PhpToJq;

class PhpToJqEn implements PhpToJqInterface
{
    public function datepickerPattern($pattern)
    {
        $pattern_php = array(
            'MMM d, y'        => "'M d, yy'",
            'EEEE, MMMM d, y' => "'DD, MM d, yy'",
            'MMMM d, y'       => "'MM d, yy'",
            'M/d/yy'          => "'m/d/y'",
        );
        
        return isset ($pattern_php[$pattern])? $pattern_php[$pattern]: "'mm/dd/yy'";
    }
    public function datetimepickerPattern($pattern)
    {
        throw new \Exception('No implementado');
    }
}