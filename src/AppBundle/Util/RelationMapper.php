<?php

namespace AppBundle\Util;

use Symfony\Component\Finder\Finder;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class RelationMapper
{
    /**
     * @var array
     */
    private $relations;

    /**
     * RelationMapper constructor.
     *
     * @param $resourcesDir
     */
    public function __construct($resourcesDir)
    {
        $finder = new Finder();
        $files = $finder->files()->name('*.yml')->in($resourcesDir);
        foreach ($files as $file) {
            $domain = substr($file->getFilename(), 0, -1 * strlen('yml') - 1);
            $this->loadResource($file, $domain);
        }
    }

    /**
     * Finds related part/model number to given $number in $domain resource or returns original $number if not found.
     *
     * @param string $number The part/model number
     * @param string $domain The domain of relation type
     *
     * @throws InvalidArgumentException If the domain not found
     *
     * @return string The mapped string
     */
    public function find($number, $domain)
    {
        if (isset($this->relations[$domain])) {
            return isset($this->relations[$domain][$number]) ? $this->relations[$domain][$number] : $number;
        }
        throw new InvalidOptionsException();
    }

    /**
     * Adds a Resource.
     *
     * @param $file
     * @param $domain
     */
    private function loadResource($file, $domain)
    {
        try {
            $this->relations[$domain] = [];
            $this->relations[$domain] = Yaml::parse(file_get_contents($file->getPathname()));
        } catch (ParseException $e) {
            throw new InvalidResourceException(sprintf('Error parsing YAML, invalid file "%s"', $file->getPathname()), 0, $e);
        }
    }
}
