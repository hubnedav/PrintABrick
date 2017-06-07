<?php

namespace Tests\AppBundle\Fixtures;


use AppBundle\Entity\Color;
use AppBundle\Entity\LDraw\Author;
use AppBundle\Entity\LDraw\Model;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadBaseData implements FixtureInterface, ContainerAwareInterface
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
        $author = new Author();
        $author->setName('Author');

        $model = new Model();
        $model->setId(1);
        $model->setAuthor($author);
        $model->setModified(new \DateTime());
        $model->setName('Name');

        $manager->persist($model);

        $manager->flush();
    }
}