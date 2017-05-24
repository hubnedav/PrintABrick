<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Category;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Subpart;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class ModelRepository extends BaseRepository
{
    public function getFilteredQueryBuilder()
    {
        $queryBuilder = $this->createQueryBuilder('model')
            ->where('model.name NOT LIKE :obsolete')
            ->setParameter('obsolete', '~%');

        return $queryBuilder;
    }

    public function findAllByCategory($category)
    {
        $queryBuilder = $this->createQueryBuilder('model')
            ->join(Category::class, 'type', Join::LEFT_JOIN, 'model.category = :category')
            ->setParameter('category', $category);

        return $queryBuilder->getQuery();
    }

    public function findOneByNumber($number)
    {
        $model = $this->createQueryBuilder('model')
            ->where('model.id LIKE :number')
            ->setParameter('number', $number)
            ->getQuery()->getOneOrNullResult();

        if (!$model) {
            $model = $this->createQueryBuilder('model')
                ->leftJoin(Alias::class, 'alias', JOIN::WITH, 'alias.model = model')
                ->where('alias.id LIKE :number')
                ->setParameter('number', $number)
                ->getQuery()->getOneOrNullResult();
        }

        return $model;
    }

    public function findOneByName($name)
    {
        return $this->findOneBy(['name' => $name]);
    }

    public function findAllRegularBySetNumber($number)
    {
        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($number);

        $queryBuilder = $this->createQueryBuilder('model');

        $queryBuilder
            ->join(Part::class, 'part', JOIN::WITH, 'part.model = model')
            ->join(Inventory_Part::class, 'inventory_part', JOIN::WITH, 'part.id = inventory_part.part')
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory_part.inventory = :inventory')
            ->setParameter('inventory', $inventory)
            ->addSelect('inventory_part')
            ->distinct(true);

        return $queryBuilder->getQuery()->getScalarResult();
    }

    public function findAllBySetNumber($number)
    {
        $queryBuilder = $this->createQueryBuilder('model');

        $queryBuilder
            ->join(Part::class, 'part', JOIN::WITH, 'part.model = model')
            ->join(Inventory_Part::class, 'inventory_part', JOIN::WITH, 'part.id = inventory_part.part')
            ->join(Inventory::class, 'inventory', JOIN::WITH, 'inventory_part.inventory = inventory.id')
            ->join(Set::class, 's', Join::WITH, 'inventory.set = s.id')
            ->where('s.id LIKE :number')
            ->setParameter('number', $number)
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllRelatedModels($number)
    {
        $queryBuilder = $this->createQueryBuilder('model');

        $queryBuilder
            ->select('related')
            ->join(Subpart::class, 'subpart', JOIN::WITH, 'model.id = subpart.subpart')
            ->join(Subpart::class, 'parent', JOIN::WITH, 'subpart.parent = parent.parent')
            ->join(Model::class, 'related', JOIN::WITH, 'related.id = parent.subpart')
            ->where('model.id = :number')
            ->setParameter('number', $number)
            ->andWhere('related.id != :number')
            ->distinct(true);

        return $queryBuilder->getQuery()->getResult();
    }

    public function count() {
        $queryBuilder = $this->createQueryBuilder('model');
        $queryBuilder->select('count(model)');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    /**
     * Create new Model entity with $number or retrieve one.
     *
     * @param $id
     *
     * @return Model
     */
    public function getOrCreate($id)
    {
        if (($model = $this->findOneBy(['id' => $id])) == null) {
            $model = new Model();
            $model->setId($id);
        }

        return $model;
    }
}
