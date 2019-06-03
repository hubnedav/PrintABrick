<?php

namespace App\Tests\Repository\LDraw;

use App\Entity\LDraw\Model;
use App\Repository\LDraw\ModelRepository;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class ModelRepositoryTest extends BaseTest
{
    /** @var ModelRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Model::class);
    }

    public function testFindOneByNumber()
    {
        $model = $this->repository->findOneByNumber('25');
        $this->assertEquals(1, $model->getId());

        $model = $this->repository->findOneByNumber(1);
        $this->assertEquals(1, $model->getId());
    }

    public function testFindOneByName()
    {
        $model = $this->repository->findOneByName('Name');
        $this->assertEquals(1, $model->getId());

        $model = $this->repository->findOneByName('Not');
        $this->assertNull($model);
    }

    public function testFindAllSiblings()
    {
        $model = $this->repository->findOneByNumber(2);
        $siblings = $this->repository->findAllSiblings($model);

        $this->assertCount(1, $siblings);
    }

    public function testCount()
    {
        $this->assertEquals(4, $this->repository->count([]));
    }

    public function testGetOrCreate()
    {
        $this->assertCount(4, $this->repository->findAll());

        $model = $this->repository->getOrCreate('25');
        $this->repository->save($model);
        $this->assertCount(4, $this->repository->findAll());

        $model = $this->repository->getOrCreate(33);
        $this->repository->save($model);
        $this->assertCount(5, $this->repository->findAll());
    }
}
