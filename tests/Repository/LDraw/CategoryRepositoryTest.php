<?php

namespace App\Tests\Repository\LDraw;

use App\Entity\LDraw\Category;
use App\Repository\LDraw\CategoryRepository;
use App\Tests\BaseTest;
use App\Tests\DataFixtures\LoadBaseData;

class CategoryRepositoryTest extends BaseTest
{
    /** @var CategoryRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->loadFixtures([LoadBaseData::class]);

        $this->repository = $this->em->getRepository(Category::class);
    }

    public function testGetOrCreate()
    {
        $this->assertCount(0, $this->repository->findAll());

        $category = $this->repository->getOrCreate('Category');
        $this->repository->save($category);
        $this->assertCount(1, $this->repository->findAll());

        $category = $this->repository->getOrCreate('Category');
        $this->repository->save($category);
        $this->assertCount(1, $this->repository->findAll());
    }
}
