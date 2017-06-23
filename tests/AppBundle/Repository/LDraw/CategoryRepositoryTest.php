<?php

namespace Tests\AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Category;
use AppBundle\Repository\LDraw\CategoryRepository;
use Tests\AppBundle\BaseTest;
use Tests\AppBundle\Fixtures\LoadBaseData;

class CategoryRepositoryTest extends BaseTest
{
    /** @var CategoryRepository */
    private $repository;

    public function setUp()
    {
        parent::setUp();
        $this->setUpDb([LoadBaseData::class]);

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
