<?php

namespace App\Repository\LDraw;

use App\Entity\LDraw\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    /**
     * Get existing entity or create new.
     *
     * @param $name
     */
    public function getOrCreate($name): Category
    {
        if ($category = $this->findByName($name)) {
            return $category;
        }

        $uow = $this->_em->getUnitOfWork()->getScheduledEntityInsertions();

        foreach ($uow as $scheduled) {
            if ($scheduled instanceof Category && $scheduled->getName() === $name) {
                return $scheduled;
            }
        }

        $category = new Category();
        $category->setName($name);

        return $category;
    }
}
