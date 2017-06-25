<?php

namespace Tests\AppBundle\Fixtures;

use AppBundle\Entity\Color;
use AppBundle\Entity\LDraw\Alias;
use AppBundle\Entity\LDraw\Author;
use AppBundle\Entity\LDraw\Model;
use AppBundle\Entity\LDraw\Subpart;
use AppBundle\Entity\Rebrickable\Inventory;
use AppBundle\Entity\Rebrickable\Inventory_Part;
use AppBundle\Entity\Rebrickable\Inventory_Set;
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

        $color3 = new Color();
        $color3->setId(-1);
        $color3->setName('Unknown');
        $color3->setRgb('EEEEEE');
        $color3->setTransparent(false);
        $manager->persist($color3);

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

        // Add sample model
        $child3 = new Model();
        $child3->setId(4);
        $child3->setAuthor($author);
        $child3->setModified(new \DateTime());
        $child3->setName('Name');
        $child3->setPath('models/1.stl');
        $manager->persist($child3);

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

        $subpart3 = new Subpart();
        $subpart3->setParent($child2);
        $subpart3->setSubpart($child3);
        $subpart3->setCount(2);
        $subpart3->setColor($color);

        $model->addSubpart($subpart2);
        $manager->persist($model);

        $child2->addSubpart($subpart3);
        $manager->persist($child2);

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
        $part2 = new Part();
        $part2->setId(2);
        $part2->setName('Name2');
        $part2->setModel($child2);
        $manager->persist($part2);

        // Add sample part
        $part3 = new Part();
        $part3->setId(3);
        $part3->setName('Name3');
        $manager->persist($part3);

        $set = new Set();
        $set->setName('Set name');
        $set->setId('8049-1');
        $set->setPartCount(1);
        $set->setYear(2011);
        $manager->persist($set);

        $set2 = new Set();
        $set2->setName('Set 2');
        $set2->setId('8055-1');
        $set2->setPartCount(2);
        $set2->setYear(2015);
        $manager->persist($set2);

        $inventory = new Inventory();
        $inventory->setSet($set);
        $inventory->setVersion(2);
        $set->addInventory($inventory);
        $manager->persist($inventory);
        $manager->persist($set);

        $inventory2 = new Inventory();
        $inventory2->setSet($set);
        $inventory2->setVersion(1);
        $set->addInventory($inventory2);
        $manager->persist($inventory2);
        $manager->persist($set);

        $inventoryPart = new Inventory_Part();
        $inventoryPart->setColor($color);
        $inventoryPart->setQuantity(4);
        $inventoryPart->setPart($part);
        $inventoryPart->setInventory($inventory);
        $inventoryPart->setSpare(false);
        $manager->persist($inventoryPart);

        $inventorySet = new Inventory_Set();
        $inventorySet->setInventory($inventory);
        $inventorySet->setSet($set2);
        $inventorySet->setQuantity(2);
        $manager->persist($inventorySet);
        $set->addInventorySet($inventorySet);
        $manager->persist($set);

        $inventoryPart = new Inventory_Part();
        $inventoryPart->setColor($color3);
        $inventoryPart->setQuantity(6);
        $inventoryPart->setPart($part2);
        $inventoryPart->setInventory($inventory);
        $inventoryPart->setSpare(true);
        $manager->persist($inventoryPart);

        $inventoryPart = new Inventory_Part();
        $inventoryPart->setColor($color2);
        $inventoryPart->setQuantity(3);
        $inventoryPart->setPart($part3);
        $inventoryPart->setInventory($inventory);
        $inventoryPart->setSpare(false);
        $manager->persist($inventoryPart);

        $inventoryPart = new Inventory_Part();
        $inventoryPart->setColor($color);
        $inventoryPart->setQuantity(1);
        $inventoryPart->setPart($part2);
        $inventoryPart->setInventory($inventory);
        $inventoryPart->setSpare(false);
        $manager->persist($inventoryPart);

        $manager->flush();
    }
}
