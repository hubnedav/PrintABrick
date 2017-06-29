<?php

namespace LoaderBundle\Util;

use Doctrine\Common\Cache\CacheProvider;
use LoaderBundle\Exception\RelationMapper\InvalidDomainException;
use LoaderBundle\Exception\RelationMapper\InvalidResourceException;
use LoaderBundle\Exception\RelationMapper\ResourceNotFoundException;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class RelationMapper
{
    /**
     * @var array
     */
    private $relations;

    /**
     * @var CacheProvider
     */
    private $cache;

    /**
     * RelationMapper constructor.
     *
     * @param CacheProvider $cache
     */
    public function __construct(CacheProvider $cache)
    {
        $this->cache = $cache;
    }

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
            if (!$data = unserialize($this->cache->fetch($domain))) {
                $data = Yaml::parse(file_get_contents($file), yaml::PARSE_KEYS_AS_STRINGS);
                $this->cache->save($domain, serialize($data), 60);
            }

            $this->relations[$domain] = [];
            $this->relations[$domain] = $data;
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
        throw new InvalidDomainException();
    }
}
