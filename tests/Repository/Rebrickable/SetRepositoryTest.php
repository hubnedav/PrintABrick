<?php

namespace App\Tests\Repository\Rebrickable;

use App\Entity\LDraw\Model;
use App\Entity\Rebrickable\Part;
use App\Entity\Rebrickable\Set;
use App\Repository\Rebrickable\SetRepository;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class SetRepositoryTest extends BaseTest
{
    /** @var SetRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Set::class);
    }

    public function testFindAllByPart()
    {
        /** @var Part $part */
        $part = $this->em->getRepository(Part::class)->find(1);

        $this->assertCount(1, $this->repository->findAllByPart($part));
    }

    public function testFindAllByModel()
    {
        /** @var Model $model */
        $model = $this->em->getRepository(Model::class)->find(1);

        $this->assertCount(1, $this->repository->findAllByModel($model));
    }

    public function testGetMinPartCount()
    {
        $this->assertEquals(1, $this->repository->getMinPartCount());
    }

    public function testGetMaxPartCount()
    {
        $this->assertEquals(2, $this->repository->getMaxPartCount());
    }

    public function testGetMinYear()
    {
        $this->assertEquals(2011, $this->repository->getMinYear());
    }

    public function testCount()
    {
        $this->assertEquals(2, $this->repository->count([]));
    }

    public function testGetMaxYear()
    {
        $this->assertEquals(2015, $this->repository->getMaxYear());
    }
}
