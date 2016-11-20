<?php
/**
 * Created by PhpStorm.
 * User: hubnedav
 * Date: 11/18/16
 * Time: 1:45 AM
 */

namespace AppBundle\Client\Rebrickable\Entity;


class Color
{
    /**
     * Internal Rebrickable color ID used
     * @var int
     */
    protected $rb_color_id;
    /**
     * Array of mapped LDraw colors
     * @var array
     */
    protected $ldraw_color_id;
    /**
     * Array of mapped BrickLink colors
     * @var array
     */
    protected $bricklink_color_id;
    /**
     * Array of mapped BrickOwl colors
     * @var array
     */
    protected $brickowl_color_id;
    /**
     * Color name
     * @var string
     */
    protected $color_name;
    /**
     * Number of parts the color appears in.
     * @var int
     */
    protected $num_parts;
    /**
     * Number of sets the color appears in.
     * @var int
     */
    protected $num_sets;
    /**
     * First year it was used.
     * @var int
     */
    protected $year1;
    /**
     * Last year it was used.
     * @var int
     */
    protected $year2;
    /**
     * Hex codes for the RGB value of this color: RRGGBB
     * @var string
     */
    protected $rgb;

    /**
     * Color constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getRbColorId()
    {
        return $this->rb_color_id;
    }

    /**
     * @param int $rb_color_id
     */
    public function setRbColorId($rb_color_id)
    {
        $this->rb_color_id = $rb_color_id;
    }

    /**
     * @return array
     */
    public function getLdrawColorId()
    {
        return $this->ldraw_color_id;
    }

    /**
     * @param array $ldraw_color_id
     */
    public function setLdrawColorId($ldraw_color_id)
    {
        $this->ldraw_color_id = $ldraw_color_id;
    }

    /**
     * @return array
     */
    public function getBricklinkColorId()
    {
        return $this->bricklink_color_id;
    }

    /**
     * @param array $bricklink_color_id
     */
    public function setBricklinkColorId($bricklink_color_id)
    {
        $this->bricklink_color_id = $bricklink_color_id;
    }

    /**
     * @return array
     */
    public function getBrickowlColorId()
    {
        return $this->brickowl_color_id;
    }

    /**
     * @param array $brickowl_color_id
     */
    public function setBrickowlColorId($brickowl_color_id)
    {
        $this->brickowl_color_id = $brickowl_color_id;
    }

    /**
     * @return string
     */
    public function getColorName()
    {
        return $this->color_name;
    }

    /**
     * @param string $color_name
     */
    public function setColorName($color_name)
    {
        $this->color_name = $color_name;
    }

    /**
     * @return int
     */
    public function getNumParts()
    {
        return $this->num_parts;
    }

    /**
     * @param int $num_parts
     */
    public function setNumParts($num_parts)
    {
        $this->num_parts = $num_parts;
    }

    /**
     * @return int
     */
    public function getNumSets()
    {
        return $this->num_sets;
    }

    /**
     * @param int $num_sets
     */
    public function setNumSets($num_sets)
    {
        $this->num_sets = $num_sets;
    }

    /**
     * @return int
     */
    public function getYear1()
    {
        return $this->year1;
    }

    /**
     * @param int $year1
     */
    public function setYear1($year1)
    {
        $this->year1 = $year1;
    }

    /**
     * @return int
     */
    public function getYear2()
    {
        return $this->year2;
    }

    /**
     * @param int $year2
     */
    public function setYear2($year2)
    {
        $this->year2 = $year2;
    }

    /**
     * @return string
     */
    public function getRgb()
    {
        return $this->rgb;
    }

    /**
     * @param string $rgbl
     */
    public function setRgb($rgb)
    {
        $this->rgb = $rgb;
    }


}