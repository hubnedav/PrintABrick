<?php

namespace AppBundle\Service;

use AppBundle\Entity\Color;
use AppBundle\Repository\ColorRepository;
use Doctrine\ORM\EntityManagerInterface;

class ColorService
{
    /** @var ColorRepository */
    private $colorRepository;

    /**
     * ColorService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->colorRepository = $em->getRepository(Color::class);
    }

    public function getAll()
    {
        return $this->colorRepository->findAll();
    }
}
