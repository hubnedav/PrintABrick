<?php

namespace AppBundle\Api\Client\Brickset\Entity;

class Set
{
    /**
     * @var int
     */
    protected $setID = null;

    /**
     * @var string
     */
    protected $number = null;

    /**
     * @var int
     */
    protected $numberVariant = null;

    /**
     * @var string
     */
    protected $name = null;

    /**
     * @var string
     */
    protected $year = null;

    /**
     * @var string
     */
    protected $theme = null;

    /**
     * @var string
     */
    protected $themeGroup = null;

    /**
     * @var string
     */
    protected $subtheme = null;

    /**
     * @var string
     */
    protected $pieces = null;

    /**
     * @var string
     */
    protected $minifigs = null;

    /**
     * @var bool
     */
    protected $image = null;

    /**
     * @var string
     */
    protected $imageFilename = null;

    /**
     * @var string
     */
    protected $thumbnailURL = null;

    /**
     * @var string
     */
    protected $largeThumbnailURL = null;

    /**
     * @var string
     */
    protected $imageURL = null;

    /**
     * @var string
     */
    protected $bricksetURL = null;

    /**
     * @var bool
     */
    protected $released = null;

    /**
     * @var bool
     */
    protected $owned = null;

    /**
     * @var bool
     */
    protected $wanted = null;

    /**
     * @var int
     */
    protected $qtyOwned = null;

    /**
     * @var string
     */
    protected $userNotes = null;

    /**
     * @var int
     */
    protected $ACMDataCount = null;

    /**
     * @var int
     */
    protected $ownedByTotal = null;

    /**
     * @var int
     */
    protected $wantedByTotal = null;

    /**
     * @var float
     */
    protected $rating = null;

    /**
     * @var int
     */
    protected $reviewCount = null;

    /**
     * @var string
     */
    protected $packagingType = null;

    /**
     * @var string
     */
    protected $availability = null;

    /**
     * @var int
     */
    protected $instructionsCount = null;

    /**
     * @var int
     */
    protected $additionalImageCount = null;

    /**
     * @var string
     */
    protected $EAN = null;

    /**
     * @var string
     */
    protected $UPC = null;

    /**
     * @var string
     */
    protected $description = null;

    /**
     * @var \DateTime
     */
    protected $lastUpdated = null;

    /**
     * Set constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return int
     */
    public function getSetID()
    {
        return $this->setID;
    }

    /**
     * @param int $setID
     *
     * @return Set
     */
    public function setSetID($setID)
    {
        $this->setID = $setID;

        return $this;
    }

    /**
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param string $number
     *
     * @return Set
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumberVariant()
    {
        return $this->numberVariant;
    }

    /**
     * @param int $numberVariant
     *
     * @return Set
     */
    public function setNumberVariant($numberVariant)
    {
        $this->numberVariant = $numberVariant;

        return $this;
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
     *
     * @return Set
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param string $year
     *
     * @return Set
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param string $theme
     *
     * @return Set
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return string
     */
    public function getThemeGroup()
    {
        return $this->themeGroup;
    }

    /**
     * @param string $themeGroup
     *
     * @return Set
     */
    public function setThemeGroup($themeGroup)
    {
        $this->themeGroup = $themeGroup;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubtheme()
    {
        return $this->subtheme;
    }

    /**
     * @param string $subtheme
     *
     * @return Set
     */
    public function setSubtheme($subtheme)
    {
        $this->subtheme = $subtheme;

        return $this;
    }

    /**
     * @return string
     */
    public function getPieces()
    {
        return $this->pieces;
    }

    /**
     * @param string $pieces
     *
     * @return Set
     */
    public function setPieces($pieces)
    {
        $this->pieces = $pieces;

        return $this;
    }

    /**
     * @return string
     */
    public function getMinifigs()
    {
        return $this->minifigs;
    }

    /**
     * @param string $minifigs
     *
     * @return Set
     */
    public function setMinifigs($minifigs)
    {
        $this->minifigs = $minifigs;

        return $this;
    }

    /**
     * @return bool
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param bool $image
     *
     * @return Set
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageFilename()
    {
        return $this->imageFilename;
    }

    /**
     * @param string $imageFilename
     *
     * @return Set
     */
    public function setImageFilename($imageFilename)
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    /**
     * @return string
     */
    public function getThumbnailURL()
    {
        return $this->thumbnailURL;
    }

    /**
     * @param string $thumbnailURL
     *
     * @return Set
     */
    public function setThumbnailURL($thumbnailURL)
    {
        $this->thumbnailURL = $thumbnailURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getLargeThumbnailURL()
    {
        return $this->largeThumbnailURL;
    }

    /**
     * @param string $largeThumbnailURL
     *
     * @return Set
     */
    public function setLargeThumbnailURL($largeThumbnailURL)
    {
        $this->largeThumbnailURL = $largeThumbnailURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getImageURL()
    {
        return $this->imageURL;
    }

    /**
     * @param string $imageURL
     *
     * @return Set
     */
    public function setImageURL($imageURL)
    {
        $this->imageURL = $imageURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getBricksetURL()
    {
        return $this->bricksetURL;
    }

    /**
     * @param string $bricksetURL
     *
     * @return Set
     */
    public function setBricksetURL($bricksetURL)
    {
        $this->bricksetURL = $bricksetURL;

        return $this;
    }

    /**
     * @return bool
     */
    public function getReleased()
    {
        return $this->released;
    }

    /**
     * @param bool $released
     *
     * @return Set
     */
    public function setReleased($released)
    {
        $this->released = $released;

        return $this;
    }

    /**
     * @return bool
     */
    public function getOwned()
    {
        return $this->owned;
    }

    /**
     * @param bool $owned
     *
     * @return Set
     */
    public function setOwned($owned)
    {
        $this->owned = $owned;

        return $this;
    }

    /**
     * @return bool
     */
    public function getWanted()
    {
        return $this->wanted;
    }

    /**
     * @param bool $wanted
     *
     * @return Set
     */
    public function setWanted($wanted)
    {
        $this->wanted = $wanted;

        return $this;
    }

    /**
     * @return int
     */
    public function getQtyOwned()
    {
        return $this->qtyOwned;
    }

    /**
     * @param int $qtyOwned
     *
     * @return Set
     */
    public function setQtyOwned($qtyOwned)
    {
        $this->qtyOwned = $qtyOwned;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserNotes()
    {
        return $this->userNotes;
    }

    /**
     * @param string $userNotes
     *
     * @return Set
     */
    public function setUserNotes($userNotes)
    {
        $this->userNotes = $userNotes;

        return $this;
    }

    /**
     * @return int
     */
    public function getACMDataCount()
    {
        return $this->ACMDataCount;
    }

    /**
     * @param int $ACMDataCount
     *
     * @return Set
     */
    public function setACMDataCount($ACMDataCount)
    {
        $this->ACMDataCount = $ACMDataCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getOwnedByTotal()
    {
        return $this->ownedByTotal;
    }

    /**
     * @param int $ownedByTotal
     *
     * @return Set
     */
    public function setOwnedByTotal($ownedByTotal)
    {
        $this->ownedByTotal = $ownedByTotal;

        return $this;
    }

    /**
     * @return int
     */
    public function getWantedByTotal()
    {
        return $this->wantedByTotal;
    }

    /**
     * @param int $wantedByTotal
     *
     * @return Set
     */
    public function setWantedByTotal($wantedByTotal)
    {
        $this->wantedByTotal = $wantedByTotal;

        return $this;
    }

    /**
     * @return float
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param float $rating
     *
     * @return Set
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return int
     */
    public function getReviewCount()
    {
        return $this->reviewCount;
    }

    /**
     * @param int $reviewCount
     *
     * @return Set
     */
    public function setReviewCount($reviewCount)
    {
        $this->reviewCount = $reviewCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getPackagingType()
    {
        return $this->packagingType;
    }

    /**
     * @param string $packagingType
     *
     * @return Set
     */
    public function setPackagingType($packagingType)
    {
        $this->packagingType = $packagingType;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * @param string $availability
     *
     * @return Set
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * @return int
     */
    public function getInstructionsCount()
    {
        return $this->instructionsCount;
    }

    /**
     * @param int $instructionsCount
     *
     * @return Set
     */
    public function setInstructionsCount($instructionsCount)
    {
        $this->instructionsCount = $instructionsCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getAdditionalImageCount()
    {
        return $this->additionalImageCount;
    }

    /**
     * @param int $additionalImageCount
     *
     * @return Set
     */
    public function setAdditionalImageCount($additionalImageCount)
    {
        $this->additionalImageCount = $additionalImageCount;

        return $this;
    }

    /**
     * @return string
     */
    public function getEAN()
    {
        return $this->EAN;
    }

    /**
     * @param string $EAN
     *
     * @return Set
     */
    public function setEAN($EAN)
    {
        $this->EAN = $EAN;

        return $this;
    }

    /**
     * @return string
     */
    public function getUPC()
    {
        return $this->UPC;
    }

    /**
     * @param string $UPC
     *
     * @return Set
     */
    public function setUPC($UPC)
    {
        $this->UPC = $UPC;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Set
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        if ($this->lastUpdated == null) {
            return null;
        }
        try {
            return new \DateTime($this->lastUpdated);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param \DateTime $lastUpdated
     *
     * @return Set
     */
    public function setLastUpdated(\DateTime $lastUpdated)
    {
        $this->lastUpdated = $lastUpdated->format(\DateTime::ATOM);

        return $this;
    }

    public function getLegoSetID()
    {
        return $this->number.'-'.$this->numberVariant;
    }
}
