<?php

namespace Tests\FrontBundle\Imagine;

use AppBundle\Api\Client\Brickset\Entity\Set;
use AppBundle\Api\Manager\BricksetManager;
use FrontBundle\Imagine\SetImageLoader;
use Tests\AppBundle\BaseTest;

class SetImageLoaderTest extends BaseTest
{
    /** @var SetImageLoader */
    private $setImageLoader;

    public function setUp()
    {
        parent::setUp();

        $bricksetManager = $this->createMock(BricksetManager::class);

        $set = new Set();
        $set->setImage(__DIR__.'/fixtures/1.png');

        $bricksetManager->method('getSetByNumber')
            ->willReturn($set);

        $this->setImageLoader = new SetImageLoader($bricksetManager, $this->filesystem);
    }

    public function testWebsite()
    {
        $this->assertNotNull($this->setImageLoader->find('4488-1.jpg'));
    }

    /**
     * @expectedException \Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException
     */
    public function testNotFound()
    {
        $this->assertNotNull($this->setImageLoader->find('123213.png'));
    }

    public function testAPI()
    {
        $this->assertNotNull($this->setImageLoader->find('4488-1.jpg'));
    }

    /**
     * @expectedException \Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException
     */
    public function testException()
    {
        $bricksetManager = $this->createMock(BricksetManager::class);

        $set = new Set();
        $set->setImage(__DIR__.'/fixtures/1.png');

        $bricksetManager->method('getSetByNumber')
            ->willReturn(null);

        $this->setImageLoader = new SetImageLoader($bricksetManager, $this->filesystem);

        $this->setImageLoader->find('-1/123213.png');
    }
}
