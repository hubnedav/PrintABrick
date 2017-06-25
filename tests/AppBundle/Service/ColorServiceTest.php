<?php

namespace Tests\AppBundle\Service;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Service\ColorService;
use AppBundle\Service\ModelService;
use AppBundle\Service\SetService;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

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
        $this->assertCount(3,$this->colorService->getAll());
    }
}