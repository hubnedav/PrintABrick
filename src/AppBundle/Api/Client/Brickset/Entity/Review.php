<?php

namespace AppBundle\Api\Client\Brickset\Entity;

class Review
{
    /**
     * @var string
     */
    protected $author = null;

    /**
     * @var \DateTime
     */
    protected $datePosted = null;

    /**
     * @var int
     */
    protected $overallRating = null;

    /**
     * @var int
     */
    protected $parts = null;

    /**
     * @var int
     */
    protected $buildingExperience = null;

    /**
     * @var int
     */
    protected $playability = null;

    /**
     * @var int
     */
    protected $valueForMoney = null;

    /**
     * @var string
     */
    protected $title = null;

    /**
     * @var string
     */
    protected $review = null;

    /**
     * @var bool
     */
    protected $HTML = null;

    /**
     * Review constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $author
     *
     * @return Review
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDatePosted()
    {
        if ($this->datePosted == null) {
            return null;
        }
        try {
            return new \DateTime($this->datePosted);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param \DateTime $datePosted
     *
     * @return Review
     */
    public function setDatePosted(\DateTime $datePosted)
    {
        $this->datePosted = $datePosted->format(\DateTime::ATOM);

        return $this;
    }

    /**
     * @return int
     */
    public function getOverallRating()
    {
        return $this->overallRating;
    }

    /**
     * @param int $overallRating
     *
     * @return Review
     */
    public function setOverallRating($overallRating)
    {
        $this->overallRating = $overallRating;

        return $this;
    }

    /**
     * @return int
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * @param int $parts
     *
     * @return Review
     */
    public function setParts($parts)
    {
        $this->parts = $parts;

        return $this;
    }

    /**
     * @return int
     */
    public function getBuildingExperience()
    {
        return $this->buildingExperience;
    }

    /**
     * @param int $buildingExperience
     *
     * @return Review
     */
    public function setBuildingExperience($buildingExperience)
    {
        $this->buildingExperience = $buildingExperience;

        return $this;
    }

    /**
     * @return int
     */
    public function getPlayability()
    {
        return $this->playability;
    }

    /**
     * @param int $playability
     *
     * @return Review
     */
    public function setPlayability($playability)
    {
        $this->playability = $playability;

        return $this;
    }

    /**
     * @return int
     */
    public function getValueForMoney()
    {
        return $this->valueForMoney;
    }

    /**
     * @param int $valueForMoney
     *
     * @return Review
     */
    public function setValueForMoney($valueForMoney)
    {
        $this->valueForMoney = $valueForMoney;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Review
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * @param string $review
     *
     * @return Review
     */
    public function setReview($review)
    {
        $this->review = $review;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHTML()
    {
        return $this->HTML;
    }

    /**
     * @param bool $HTML
     *
     * @return Review
     */
    public function setHTML($HTML)
    {
        $this->HTML = $HTML;

        return $this;
    }
}
