<?php

namespace Tests\FrontBundle\Imagine;

use AppBundle\Api\Client\Rebrickable\Entity\Part;
use AppBundle\Api\Manager\RebrickableManager;
use FrontBundle\Imagine\PartImageLoader;
use Tests\AppBundle\BaseTest;

class PartImageLoaderTest extends BaseTest
{
    /** @var PartImageLoader */
    private $partImageLoader;

    public function setUp()
    {
        parent::setUp();

        $rebrickableManager = $this->createMock(RebrickableManager::class);

        $part = new Part();
        $part->setImgUrl(__DIR__.'/fixtures/1.png');

        $rebrickableManager->method('getPart')
            ->willReturn($part);

        $this->partImageLoader = new PartImageLoader($rebrickableManager, $this->filesystem);
    }

    public function testLocal()
    {
        $this->filesystem->write('images/-1/1.png', file_get_contents(__DIR__.'/fixtures/1.png'));
        $this->assertNotNull($this->partImageLoader->find('-1/1.png'));
    }

    public function testWebsite()
    {
        $this->assertNotNull($this->partImageLoader->find('-1/1.png'));
    }

    public function testAPI()
    {
        $this->assertNotNull($this->partImageLoader->find('-1/123213.png'));
    }

    /**
     * @expectedException \Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException
     */
    public function testNotFound()
    {
        $this->assertNotNull($this->partImageLoader->find('123213.png'));
    }

    /**
     * @expectedException \Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException
     */
    public function testException()
    {
        $rebrickableManager = $this->createMock(RebrickableManager::class);
        $rebrickableManager->method('getPart')
            ->willThrowException(new \Exception());

        $this->partImageLoader = new PartImageLoader($rebrickableManager, $this->filesystem);

        $this->partImageLoader->find('-1/123213.png');
    }
}
