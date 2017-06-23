<?php

namespace Tests\AppBundle\Fixtures;

use AppBundle\Entity\Color;
use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Author;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Subpart;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Part;
use AppBundle\Entity\Rebrickable\Set;
use Doctrine\Common\Collections\ArrayCollection;
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
        $color = new Color();
        $color->setId(1);
        $color->setName('Black');
        $color->setRgb('000000');
        $color->setTransparent(false);
        $manager->persist($color);

        $color2 = new Color();
        $color2->setId(2);
        $color2->setName('Blue');
        $color2->setRgb('EEEEEE');
        $color2->setTransparent(false);
        $manager->persist($color2);

        $color2 = new Color();
        $color2->setId(-1);
        $color2->setName('Unknown');
        $color2->setRgb('EEEEEE');
        $color2->setTransparent(false);
        $manager->persist($color2);

        // Add sample author
        $author = new Author();
        $author->setName('Author');
        $manager->persist($author);

        // Add sample model
        $model = new Model();
        $model->setId(1);
        $model->setAuthor($author);
        $model->setModified(new \DateTime());
        $model->setName('Name');
        $model->setPath('models/1.stl');
        $manager->persist($model);

        // Add sample model
        $child = new Model();
        $child->setId(2);
        $child->setAuthor($author);
        $child->setModified(new \DateTime());
        $child->setName('Name2');
        $child->setPath('models/1.stl');
        $manager->persist($child);

        // Add sample model
        $child2 = new Model();
        $child2->setId(3);
        $child2->setAuthor($author);
        $child2->setModified(new \DateTime());
        $child2->setName('Name2');
        $child2->setPath('models/1.stl');
        $manager->persist($child);

        $subpart = new Subpart();
        $subpart->setParent($model);
        $subpart->setSubpart($child);
        $subpart->setCount(2);
        $subpart->setColor($color);
        $model->addSubpart($subpart);

        $subpart2 = new Subpart();
        $subpart2->setParent($model);
        $subpart2->setSubpart($child2);
        $subpart2->setCount(2);
        $subpart2->setColor($color);

        $model->addSubpart($subpart2);
        $manager->persist($model);

        // Add sample model
        $alias = new Alias();
        $alias->setId('2d');
        $alias->setModel($model);
        $manager->persist($alias);

        // Add sample model
        $alias = new Alias();
        $alias->setId('25');
        $alias->setModel($model);
        $manager->persist($alias);

        // Add sample part
        $part = new Part();
        $part->setId(1);
        $part->setName('Name');
        $part->setModel($model);
        $manager->persist($part);

        // Add sample part
        $part = new Part();
        $part->setId(2);
        $part->setName('Name2');
        $manager->persist($part);

        $set = new Set();
        $set->setName('Set name');
        $set->setId('8049-1');
        $set->setPartCount(1);
        $set->setYear(2011);
        $manager->persist($set);

        $inventory = new Inventory();
        $inventory->setSet($set);
        $inventory->setVersion(1);
        $set->addInventory($inventory);
        $manager->persist($inventory);
        $manager->persist($set);

        $inventoryPart = new Inventory_Part();
        $inventoryPart->setColor($color);
        $inventoryPart->setQuantity(5);
        $inventoryPart->setPart($part);
        $inventoryPart->setInventory($inventory);
        $inventoryPart->setSpare(false);
        $manager->persist($inventoryPart);

        $manager->flush();
    }
}
