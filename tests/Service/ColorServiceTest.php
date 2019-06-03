<?php

namespace App\Tests\Service;

use App\Service\ColorService;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class ColorServiceTest extends BaseTest
{
    /** @var ColorService */
    private $colorService;

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures([
            LoadBaseData::class,
        ]);

        $this->colorService = new ColorService($this->em);
    }

    public function testGetAll()
    {
        $this->assertCount(3, $this->colorService->getAll());
    }
}
