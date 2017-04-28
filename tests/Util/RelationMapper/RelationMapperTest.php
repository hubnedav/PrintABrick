<?php

namespace Tests\AppBundle\Util\RelationMapper;

use AppBundle\Util\RelationMapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

class RelationMapperTest extends TestCase
{
    public function testLoad()
    {
        $mapper = new RelationMapper(__DIR__.'/fixtures/');

        $this->assertEquals('bar', $mapper->find('foo','resources'));
    }

//    public function testLoadDoesNothingIfEmpty()
//    {
//        $resource = __DIR__.'/fixtures/empty.yml';
//        $catalogue = Yaml::parse(file_get_contents($resource));
//
//        $this->assertEquals(array(), $catalogue);
//    }
//
//    /**
//     * @expectedException \Symfony\Component\Translation\Exception\NotFoundResourceException
//     */
//    public function testLoadNonExistingResource()
//    {
//        $loader = new YamlFileLoader();
//        $resource = __DIR__.'/../fixtures/non-existing.yml';
//        $loader->load($resource, 'en', 'domain1');
//    }
//
//    /**
//     * @expectedException \Symfony\Component\Translation\Exception\InvalidResourceException
//     */
//    public function testLoadThrowsAnExceptionIfFileNotLocal()
//    {
//        $loader = new YamlFileLoader();
//        $resource = 'http://example.com/resources.yml';
//        $loader->load($resource, 'en', 'domain1');
//    }
//
//    /**
//     * @expectedException \Symfony\Component\Translation\Exception\InvalidResourceException
//     */
//    public function testLoadThrowsAnExceptionIfNotAnArray()
//    {
//        $loader = new YamlFileLoader();
//        $resource = __DIR__.'/../fixtures/non-valid.yml';
//        $loader->load($resource, 'en', 'domain1');
//    }
}