<?php

namespace App\Repository\Rebrickable;

use App\Entity\LDraw\Model;
use App\Entity\Rebrickable\Inventory;
use App\Entity\Rebrickable\Inventory_Part;
use App\Entity\Rebrickable\Part;
use App\Entity\Rebrickable\Set;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class Inventory_PartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inventory_Part::class);
    }

    /**
     * @param null $spare
     * @param null $model
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilderMatching(Set $set, $spare = null, $model = null)
    {
        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($set->getId());

        $queryBuilder = $this->createQueryBuilder('inventory_part')
            ->leftJoin('inventory_part.part', 'part')
            ->where('inventory_part.inventory = :inventory')
            ->setParameter('inventory', $inventory)
//            ->andWhere('part.category != 58')
;

        if (null !== $spare) {
            $queryBuilder
                ->andWhere('inventory_part.spare = :spare')
                ->setParameter('spare', $spare);
        }

        if (null !== $model) {
            if (true === $model) {
                $queryBuilder
                    ->leftjoin('part.model', 'model')
                    ->andWhere('model IS NOT NULL');
            } else {
                $queryBuilder
                    ->leftJoin('part.model', 'model')
                    ->andWhere('model IS NULL');
            }
        }

        return $queryBuilder;
    }

    /**
     * Finds all inventoty_parts in newest inventory of set.
     *
     * @param bool $spare If true - find all spare parts, false - find all regular parts, null - spare and regular parts
     * @param bool $model If true - find all parts with model relation, false - find all parts without model relation, null - all parts
     *
     * @return array
     */
    public function getAllMatching(Set $set, $spare = null, $model = null)
    {
        $queryBuilder = $this->getQueryBuilderMatching($set, $spare, $model);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Get total part count of Set.
     *
     * @param bool $spare If true - find all spare parts, false - find all regular parts, null - spare and regular parts
     * @param bool $model If true - find all parts with model relation, false - find all parts without model relation, null - all parts
     *
     * @return mixed
     */
    public function getPartCount(Set $set, $spare = null, $model = null)
    {
        $queryBuilder = $this->getQueryBuilderMatching($set, $spare, $model);
        $queryBuilder->select('SUM(inventory_part.quantity) as parts');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
