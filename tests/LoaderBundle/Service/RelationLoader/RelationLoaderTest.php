<?php

namespace Tests\LoaderBundle\Service;

use AppBundle\Entity\Rebrickable\Part;
use League\Flysystem\File;
use LoaderBundle\Service\RelationLoader;
use LoaderBundle\Util\RelationMapper;
use Symfony\Component\Console\Output\NullOutput;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadUnmappedData;

class RelationLoaderTest extends BaseTest
{
    /**
     * @var RelationLoader
     */
    private $relationLoader;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadUnmappedData::class]);

        $file = $this->createMock(File::class);
        $file->method('getPath')->willReturn('path');

        $relationMapper = $this->createMock(RelationMapper::class);
        $relationMapper->method('find')->will($this->returnArgument(0));

        $this->relationLoader = new RelationLoader($this->get('doctrine.orm.entity_manager'), $this->get('monolog.logger.event'), $relationMapper);
        $this->relationLoader->setOutput(new NullOutput());
    }

    public function testLoadAll()
    {
        $this->relationLoader->loadAll();

        $parts = $this->em->getRepository(Part::class)->findAll();
        foreach ($parts as $part) {
            $this->assertNotNull($part->getModel());
        }
    }

    public function testLoadNotPaired()
    {
        $this->relationLoader->loadNotPaired();

        $parts = $this->em->getRepository(Part::class)->findAll();
        foreach ($parts as $part) {
            $this->assertNotNull($part->getModel());
        }
    }
}
