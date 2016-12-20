<?php

namespace AppBundle\Api\Client\Rebrickable\Entity;

class Part
{
    /**
     * Part ID.
     *
     * @var int
     */
    private $id;
    /**
     * Quantity of part in set returned from getSetParts.
     *
     * @var int
     */
    private $qty;
    /**
     * Part Name.
     *
     * @var string
     */
    private $name;
    /**
     * Part type 1 = normal part, 2 = spare part.
     *
     * @var int
     */
    private $type;
    /**
     * Year the part first appeared in sets.
     *
     * @var int
     */
    private $year1;
    /**
     * Year the part was last seen in sets.
     *
     * @var int
     */
    private $year2;
    /**
     * Part category/type description.
     *
     * @var string
     */
    private $category;
    /**
     * Array of colors the part appears in.
     *
     * @var array
     */
    private $colors;
    /**
     * Array of related Part IDs used by external systems.
     *
     * @var array
     */
    private $external_part_ids;
    /**
     * Rebrickable URL to the Part Details page.
     *
     * @var string
     */
    private $part_url;
    /**
     * Rebrickable URL of the main part image (tries to use most common color).
     *
     * @var string
     */
    private $part_img_url;

    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $part_id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param $qty
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    /**
     * @return mixed
     */
    public function getYear1()
    {
        return $this->year1;
    }

    /**
     * @param mixed $year1
     */
    public function setYear1($year1)
    {
        $this->year1 = $year1;
    }

    /**
     * @return mixed
     */
    public function getYear2()
    {
        return $this->year2;
    }

    /**
     * @param mixed $year2
     */
    public function setYear2($year2)
    {
        $this->year2 = $year2;
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }
    /**
     * @return mixed
     */
    public function getColors()
    {
        return $this->colors;
    }

    /**
     * @param mixed $colors
     */
    public function setColors($colors)
    {
        $this->colors = $colors;
    }
    /**
     * @return mixed
     */
    public function getExternalPartIds()
    {
        return $this->external_part_ids;
    }

    /**
     * @param mixed $external_part_ids
     */
    public function setExternalPartIds($external_part_ids)
    {
        $this->external_part_ids = $external_part_ids;
    }

    /**
     * @return mixed
     */
    public function getPartUrl()
    {
        return $this->part_url;
    }

    /**
     * @param mixed $part_url
     */
    public function setPartUrl($part_url)
    {
        $this->part_url = $part_url;
    }

    /**
     * @return mixed
     */
    public function getPartImgUrl()
    {
        return $this->part_img_url;
    }

    /**
     * @param $part_img_url
     */
    public function setPartImgUrl($part_img_url)
    {
        $this->part_img_url = $part_img_url;
    }
}
