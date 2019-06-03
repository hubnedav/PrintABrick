<?php

namespace App\Transformer;

class FormatTransformer
{
    /**
     * Transform bytes count to human readable format.
     *
     * @param $bytes
     * @param int $precision
     *
     * @return string
     */
    public function bytesToSize($bytes, $precision = 2): string
    {
        if (0 === $bytes) {
            return round(0, $precision).' B';
        }

        $suffix = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $exponent = floor(log($bytes, 1024));

        return round($bytes / (1024 ** $exponent), $precision).' '.$suffix[(int) $exponent];
    }
}
