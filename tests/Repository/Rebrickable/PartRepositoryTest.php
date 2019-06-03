<?php

namespace App\Tests\Repository\Rebrickable;

use App\Entity\Rebrickable\Part;
use App\Repository\Rebrickable\PartRepository;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class PartRepositoryTest extends BaseTest
{
    /** @var PartRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Part::class);
    }

    public function testFindAllNotPaired()
    {
        $this->assertCount(1, $this->repository->findAllNotPaired());
    }
}
