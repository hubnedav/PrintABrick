<?php

namespace AppBundle\Transformer;


class FormatTransformer
{
    function bytesToSize($bytes, $precision = 2)
    {
        if ($bytes == 0)
            return "0.00 B";

        $suffix = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $exponent = floor(log($bytes, 1024));

        return round($bytes/pow(1024, $exponent), $precision).' '.$suffix[(int)$exponent];
    }
}