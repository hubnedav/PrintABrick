<?php

namespace Tests\AppBundle\Util\RelationMapper;

use AppBundle\Util\RelationMapper;
use Doctrine\Common\Cache\ArrayCache;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

class RelationMapperTest extends TestCase
{
    public function testLoad()
    {
        $mapper = new RelationMapper(new ArrayCache());
        $mapper->loadResource(__DIR__ . '/fixtures/resources.yml', 'resources');

        $this->assertEquals('bar', $mapper->find('foo','resources'));
        $this->assertEquals('bar', $mapper->find('bar','resources'));
    }

    /**
     * @expectedException AppBundle\Exception\RelationMapper\ResourceNotFoundException
     */
    public function testLoadNonExistingResource()
    {
        $mapper = new RelationMapper(new ArrayCache());
        $resource = __DIR__.'/fixtures/non-existing.yml';
        $mapper->loadResource($resource, 'resources');
    }

    /**
     * @expectedException AppBundle\Exception\RelationMapper\InvalidResourceException
     */
    public function testLoadInvalidResource()
    {
        $mapper = new RelationMapper(new ArrayCache());
        $resource = __DIR__ . '/fixtures/invalid.yml';
        $mapper->loadResource($resource, 'resources');
    }
}