<?php

namespace Tests\LoaderBundle\Util\RelationMapper;

use Doctrine\Common\Cache\ArrayCache;
use LoaderBundle\Util\RelationMapper;
use PHPUnit\Framework\TestCase;

class RelationMapperTest extends TestCase
{
    public function testLoad()
    {
        $mapper = new RelationMapper(new ArrayCache());
        $mapper->loadResource(__DIR__.'/fixtures/resources.yml', 'resources');

        $this->assertEquals('bar', $mapper->find('foo', 'resources'));
        $this->assertEquals('bar', $mapper->find('bar', 'resources'));
    }

    /**
     * @expectedException \LoaderBundle\Exception\RelationMapper\ResourceNotFoundException
     */
    public function testLoadNonExistingResource()
    {
        $mapper = new RelationMapper(new ArrayCache());
        $resource = __DIR__.'/fixtures/non-existing.yml';
        $mapper->loadResource($resource, 'resources');
    }

    /**
     * @expectedException \LoaderBundle\Exception\RelationMapper\InvalidResourceException
     */
    public function testLoadInvalidResource()
    {
        $mapper = new RelationMapper(new ArrayCache());
        $resource = __DIR__.'/fixtures/invalid.yml';
        $mapper->loadResource($resource, 'resources');
    }

    /**
     * @expectedException \LoaderBundle\Exception\RelationMapper\InvalidDomainException
     */
    public function testLoadInvalidDomain()
    {
        $mapper = new RelationMapper(new ArrayCache());
        $mapper->loadResource(__DIR__.'/fixtures/resources.yml', 'resources');

        $mapper->find('foo', 'incorect');
    }
}
