<?php

namespace App\Tests\DataFixtures;

use App\Entity\Color;
use App\Entity\LDraw\Author;
use App\Entity\LDraw\Model;
use App\Entity\Rebrickable\Inventory;
use App\Entity\Rebrickable\Inventory_Part;
use App\Entity\Rebrickable\Part;
use App\Entity\Rebrickable\Set;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUnmappedData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Add sample author
        $author = new Author();
        $author->setName('Author');
        $manager->persist($author);

        // Add sample model
        $model = new Model();
        $model->setId('930');
        $model->setAuthor($author);
        $model->setModified(new \DateTime());
        $model->setName('Name');
        $model->setPath('models/1.stl');
        $manager->persist($model);

        // Add sample model
        $model = new Model();
        $model->setId('973c00');
        $model->setAuthor($author);
        $model->setModified(new \DateTime());
        $model->setName('ModelName');
        $model->setPath('models/1.stl');
        $manager->persist($model);

        // Add sample model
        $model = new Model();
        $model->setId('970c00');
        $model->setAuthor($author);
        $model->setModified(new \DateTime());
        $model->setName('Name 3');
        $model->setPath('models/1.stl');
        $manager->persist($model);

        // Add sample part
        $part = new Part();
        $part->setId('930');
        $part->setName('Name');
        $manager->persist($part);

        // Add sample part
        $part = new Part();
        $part->setId('930p01');
        $part->setName('Part2');
        $manager->persist($part);

        // Add sample part
        $part = new Part();
        $part->setId('930pr002');
        $part->setName('Part2');
        $manager->persist($part);

        // Add sample part
        $part = new Part();
        $part->setId('930pb001a');
        $part->setName('Part2');
        $part->setModel($model);
        $manager->persist($part);

        // Add sample part
        $part = new Part();
        $part->setId('973c05');
        $part->setName('Part2');
        $manager->persist($part);

        // Add sample part
        $part = new Part();
        $part->setId('1235');
        $part->setName('ModelName');
        $manager->persist($part);

        // Add sample part
        $part = new Part();
        $part->setId('970c52');
        $part->setName('part');
        $manager->persist($part);

        $set = new Set();
        $set->setName('Set');
        $set->setId('8049-1');
        $set->setPartCount(1);
        $set->setYear(2011);
        $manager->persist($set);

        $color = new Color();
        $color->setId(1);
        $color->setName('Black');
        $color->setRgb('000000');
        $color->setTransparent(false);
        $manager->persist($color);

        $inventory = new Inventory();
        $inventory->setSet($set);
        $inventory->setVersion(1);
        $manager->persist($inventory);

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
