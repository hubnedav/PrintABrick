<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class Inventory_PartRepository extends BaseRepository
{
    /**
     * @param Set $set
     * @param null $spare
     * @param null $model
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilderMatching(Set $set, $spare = null, $model = null) {
        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($set->getId());

        $queryBuilder = $this->createQueryBuilder('inventory_part')
            ->where('inventory_part.inventory = :inventory')
            ->setParameter('inventory', $inventory->getId())
            ->join(Part::class, 'part', JOIN::WITH, 'inventory_part.part = part');


        if ($spare !== null) {
            $queryBuilder
                ->andWhere('inventory_part.spare = :spare')
                ->setParameter('spare', $spare);
        }

        if ($model !== null) {
            if ($model === true) {
                $queryBuilder->andWhere('part.model IS NOT NULL');
            } else {
                $queryBuilder->andWhere('part.model IS NULL');
            }
        }
        return $queryBuilder;
    }

    /**
     * Finds all inventoty_parts in newest inventory of set.
     *
     * @param Set  $set
     * @param bool   $spare  If true - find all spare parts, false - find all regular parts, null - spare and regular parts
     * @param bool   $model  If true - find all parts with model relation, false - find all parts without model relation, null - all parts
     *
     * @return array
     */
    public function getAllMatching(Set $set, $spare = null, $model = null)
    {
        $queryBuilder = $this->getQueryBuilderMatching($set,$spare,$model);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Get total part count of Set.
     *
     * @param Set  $set
     * @param bool $spare If true - find all spare parts, false - find all regular parts, null - spare and regular parts
     * @param bool $model If true - find all parts with model relation, false - find all parts without model relation, null - all parts
     *
     * @return mixed
     */
    public function getPartCount(Set $set, $spare = null, $model = null)
    {
        $queryBuilder = $this->getQueryBuilderMatching($set,$spare,$model);
        $queryBuilder->select('SUM(inventory_part.quantity) as parts');

        return $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
