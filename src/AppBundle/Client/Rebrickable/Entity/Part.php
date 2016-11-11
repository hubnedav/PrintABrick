<?php
/**
 * Created by PhpStorm.
 * User: hubnedav
 * Date: 11/11/16
 * Time: 12:45 AM
 */

namespace AppBundle\Client\Rebrickable\Entity;


class Part
{
	protected $id;
	protected $name;
	protected $category;
	protected $typeId;
	protected $colors;
	protected $external_part_ids;
	protected $part_url;
	protected $part_img_url;

	public function __construct($part_id, $part_name, $category, $part_type_id, $colors, $external_part_ids, $part_url, $part_img_url)
	{
		$this->id           = $part_id;
		$this->name              = $part_name;
		$this->category          = $category;
		$this->typeId      = $part_type_id;
		$this->colors            = $colors;
		$this->external_part_ids = $external_part_ids;
		$this->part_url          = $part_url;
		$this->part_img_url      = $part_img_url;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
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
	public function getTypeId()
	{
		return $this->typeId;
	}

	/**
	 * @param mixed $typeId
	 */
	public function setTypeId($typeId)
	{
		$this->typeId = $typeId;
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
	 * @param mixed $part_img_url
	 */
	public function setPartImgUrl($part_img_url)
	{
		$this->part_img_url = $part_img_url;
	}
}