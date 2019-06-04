<?php

namespace App\Twig;

use App\Transformer\FormatTransformer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /** @var FormatTransformer */
    private $formatTransformer;

    /**
     * AppExtension constructor.
     *
     * @param FormatTransformer $formatTransformer
     */
    public function __construct(FormatTransformer $formatTransformer)
    {
        $this->formatTransformer = $formatTransformer;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('bytesToSize', [$this, 'bytesToSize']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('remoteSize', [$this, 'remoteSize']),
            new TwigFunction('remoteFilename', [$this, 'remoteFilename']),
        ];
    }

    public function bytesToSize($bytes, $precision = 2): string
    {
        return $this->formatTransformer->bytesToSize($bytes, $precision);
    }

    public function remoteSize($url)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

        curl_close($ch);

        return $size;
    }

    public function remoteFilename($url): string
    {
        return basename($url);
    }
}
