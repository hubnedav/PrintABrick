<?php

namespace AppBundle\Repository\LDraw;

use AppBundle\Entity\Color;
use AppBundle\Entity\LDraw\Subpart;
use AppBundle\Repository\BaseRepository;

class SubpartRepository extends BaseRepository
{
    /**
     * Create new Subpart relation entity or retrieve one by foreign keys.
     *
     * @param $parent
     * @param $child
     * @param $count
     * @param $colorId
     *
     * @return Subpart
     */
    public function getOrCreate($parent, $child, $count, $colorId)
    {
        if (($subpart = $this->find(['parent' => $parent, 'subpart' => $child, 'color' => $colorId]))) {
            $subpart->setCount($count);
        } else {
            $subpart = new Subpart();

            $colorRepository = $this->getEntityManager()->getRepository(Color::class);
            if (!($color = $colorRepository->find($colorId))) {
                /** @var Color $color */
                $color = $colorRepository->find(-1);
            }

            $subpart
                ->setParent($parent)
                ->setSubpart($child)
                ->setCount($count)
                ->setColor($color);
        }

        return $subpart;
    }
}
