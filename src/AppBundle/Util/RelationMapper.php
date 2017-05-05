<?php

namespace AppBundle\Util;

use AppBundle\Exception\RelationMapper\InvalidResourceException;
use AppBundle\Exception\RelationMapper\ResourceNotFoundException;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class RelationMapper
{
    /**
     * @var array
     */
    private $relations;

    /**
     * Adds a Resource.
     *
     * @param $file
     * @param $domain
     *
     * @throws InvalidResourceException|ResourceNotFoundException
     */
    public function loadResource($file, $domain)
    {
        if (!file_exists($file)) {
            throw new ResourceNotFoundException($file);
        }

        try {
            $this->relations[$domain] = [];
            $this->relations[$domain] = Yaml::parse(file_get_contents($file));
        } catch (ParseException $e) {
            throw new InvalidResourceException(sprintf('Error parsing YAML, invalid file "%s"', $file), 0, $e);
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
}
