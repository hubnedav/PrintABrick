<?php

namespace AppBundle\Repository\Rebrickable;

use AppBundle\Entity\LDraw\Category;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Repository\BaseRepository;
use Doctrine\ORM\Query\Expr\Join;

class Inventory_PartRepository extends BaseRepository
{
    /**
     * Finds all inventoty_parts in newest inventory of set.
     *
     * @param string $number Unique number identifier of set
     * @param bool $spare If true - find all spare parts, false - find all regular parts, null - spare and regular parts
     * @param bool $model If true - find all parts with model relation, false - find all parts without model relation, null - all parts
     * @return array
     */
    public function findAllBySetNumber($number, $spare = null, $model = null)
    {
        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($number);

        $queryBuilder = $this->createQueryBuilder('inventory_part')
            ->where('inventory_part.inventory = :inventory')
            ->setParameter('inventory', $inventory);


        if($spare !== null) {
            $queryBuilder
                ->andWhere('inventory_part.spare = :spare')
                ->setParameter('spare', $spare);
        }

        if($model !== null) {
            $queryBuilder
                ->join(Part::class, 'part', JOIN::WITH, 'inventory_part.part = part');
            if($model === true) {
                $queryBuilder->andWhere('part.model IS NOT NULL');
            } else {
                $queryBuilder->andWhere('part.model IS NULL');
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllBySetNumberAndColor($number, $color)
    {
        $inventory = $this->getEntityManager()->getRepository(Inventory::class)->findNewestInventoryBySetNumber($number);

        $queryBuilder = $this->createQueryBuilder('inventory_part')
            ->where('inventory_part.inventory = :inventory')
            ->setParameter('inventory', $inventory)
            ->andWhere('inventory_part.color = :color')
            ->setParameter('color', $color);

        return $queryBuilder->getQuery()->getResult();
    }
}
