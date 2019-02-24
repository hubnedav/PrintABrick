<?php

namespace App\Api\Client\Rebrickable\Entity;

class Color
{
    /**
     * Internal Rebrickable color ID.
     *
     * @var int
     */
    private $id;
    /**
     * Color name.
     *
     * @var string
     */
    private $name;
    /**
     * RGB hex code.
     *
     * @var string
     */
    private $rgb;
    /**
     * Wether color is transparent.
     *
     * @var bool
     */
    private $isTrans;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getRgb()
    {
        return $this->rgb;
    }

    /**
     * @param string $rgb
     */
    public function setRgb($rgb)
    {
        $this->rgb = $rgb;
    }

    /**
     * @return bool
     */
    public function isIsTrans()
    {
        return $this->isTrans;
    }

    /**
     * @param bool $isTrans
     */
    public function setIsTrans($isTrans)
    {
        $this->isTrans = $isTrans;
    }
}
