<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Color;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadColors implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $colors = fopen($this->container->getParameter('kernel.root_dir').'/Resources/fixtures/colors.csv', 'r');

        while (!feof($colors)) {
            $line = fgetcsv($colors);

            if ($line) {
                $color = new Color();
                $color->setId($line[0]);
                $color->setRgb($line[1]);
                $color->setTransparent($line[2]);
                $color->setName($line[3]);

                $manager->persist($color);
            }
        }

        fclose($colors);

        $manager->flush();
    }
}
