<?php

namespace App\Service;

use App\Entity\Color;
use App\Repository\ColorRepository;
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
