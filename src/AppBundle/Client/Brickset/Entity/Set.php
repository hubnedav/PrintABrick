<?php

namespace AppBundle\Client\Brickset\Entity;

class Set
{
    /**
     * @var int $setID
     */
    protected $setID = null;

    /**
     * @var string $number
     */
    protected $number = null;

    /**
     * @var int $numberVariant
     */
    protected $numberVariant = null;

    /**
     * @var string $name
     */
    protected $name = null;

    /**
     * @var string $year
     */
    protected $year = null;

    /**
     * @var string $theme
     */
    protected $theme = null;

    /**
     * @var string $themeGroup
     */
    protected $themeGroup = null;

    /**
     * @var string $subtheme
     */
    protected $subtheme = null;

    /**
     * @var string $pieces
     */
    protected $pieces = null;

    /**
     * @var string $minifigs
     */
    protected $minifigs = null;

    /**
     * @var boolean $image
     */
    protected $image = null;

    /**
     * @var string $imageFilename
     */
    protected $imageFilename = null;

    /**
     * @var string $thumbnailURL
     */
    protected $thumbnailURL = null;

    /**
     * @var string $largeThumbnailURL
     */
    protected $largeThumbnailURL = null;

    /**
     * @var string $imageURL
     */
    protected $imageURL = null;

    /**
     * @var string $bricksetURL
     */
    protected $bricksetURL = null;

    /**
     * @var boolean $released
     */
    protected $released = null;

    /**
     * @var boolean $owned
     */
    protected $owned = null;

    /**
     * @var boolean $wanted
     */
    protected $wanted = null;

    /**
     * @var int $qtyOwned
     */
    protected $qtyOwned = null;

    /**
     * @var string $userNotes
     */
    protected $userNotes = null;

    /**
     * @var int $ACMDataCount
     */
    protected $ACMDataCount = null;

    /**
     * @var int $ownedByTotal
     */
    protected $ownedByTotal = null;

    /**
     * @var int $wantedByTotal
     */
    protected $wantedByTotal = null;

    /**
     * @var string $UKRetailPrice
     */
    protected $UKRetailPrice = null;

    /**
     * @var string $USRetailPrice
     */
    protected $USRetailPrice = null;

    /**
     * @var string $CARetailPrice
     */
    protected $CARetailPrice = null;

    /**
     * @var string $EURetailPrice
     */
    protected $EURetailPrice = null;

    /**
     * @var string $USDateAddedToSAH
     */
    protected $USDateAddedToSAH = null;

    /**
     * @var string $USDateRemovedFromSAH
     */
    protected $USDateRemovedFromSAH = null;

    /**
     * @var float $rating
     */
    protected $rating = null;

    /**
     * @var int $reviewCount
     */
    protected $reviewCount = null;

    /**
     * @var string $packagingType
     */
    protected $packagingType = null;

    /**
     * @var string $availability
     */
    protected $availability = null;

    /**
     * @var int $instructionsCount
     */
    protected $instructionsCount = null;

    /**
     * @var int $additionalImageCount
     */
    protected $additionalImageCount = null;

    /**
     * @var string $EAN
     */
    protected $EAN = null;

    /**
     * @var string $UPC
     */
    protected $UPC = null;

    /**
     * @var string $description
     */
    protected $description = null;

    /**
     * @var \DateTime $lastUpdated
     */
    protected $lastUpdated = null;

    /**
     * @param int $setID
     * @param int $numberVariant
     * @param boolean $image
     * @param boolean $released
     * @param boolean $owned
     * @param boolean $wanted
     * @param int $qtyOwned
     * @param int $ACMDataCount
     * @param int $ownedByTotal
     * @param int $wantedByTotal
     * @param float $rating
     * @param int $reviewCount
     * @param int $instructionsCount
     * @param int $additionalImageCount
     * @param \DateTime $lastUpdated
     */
    public function __construct($setID, $numberVariant, $image, $released, $owned, $wanted, $qtyOwned, $ACMDataCount, $ownedByTotal, $wantedByTotal, $rating, $reviewCount, $instructionsCount, $additionalImageCount, \DateTime $lastUpdated)
    {
      $this->setID = $setID;
      $this->numberVariant = $numberVariant;
      $this->image = $image;
      $this->released = $released;
      $this->owned = $owned;
      $this->wanted = $wanted;
      $this->qtyOwned = $qtyOwned;
      $this->ACMDataCount = $ACMDataCount;
      $this->ownedByTotal = $ownedByTotal;
      $this->wantedByTotal = $wantedByTotal;
      $this->rating = $rating;
      $this->reviewCount = $reviewCount;
      $this->instructionsCount = $instructionsCount;
      $this->additionalImageCount = $additionalImageCount;
      $this->lastUpdated = $lastUpdated->format(\DateTime::ATOM);
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
     * @return Set
     */
    public function setMinifigs($minifigs)
    {
      $this->minifigs = $minifigs;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getImage()
    {
      return $this->image;
    }

    /**
     * @param boolean $image
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
     * @return Set
     */
    public function setBricksetURL($bricksetURL)
    {
      $this->bricksetURL = $bricksetURL;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getReleased()
    {
      return $this->released;
    }

    /**
     * @param boolean $released
     * @return Set
     */
    public function setReleased($released)
    {
      $this->released = $released;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getOwned()
    {
      return $this->owned;
    }

    /**
     * @param boolean $owned
     * @return Set
     */
    public function setOwned($owned)
    {
      $this->owned = $owned;
      return $this;
    }

    /**
     * @return boolean
     */
    public function getWanted()
    {
      return $this->wanted;
    }

    /**
     * @param boolean $wanted
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
     * @return Set
     */
    public function setWantedByTotal($wantedByTotal)
    {
      $this->wantedByTotal = $wantedByTotal;
      return $this;
    }

    /**
     * @return string
     */
    public function getUKRetailPrice()
    {
      return $this->UKRetailPrice;
    }

    /**
     * @param string $UKRetailPrice
     * @return Set
     */
    public function setUKRetailPrice($UKRetailPrice)
    {
      $this->UKRetailPrice = $UKRetailPrice;
      return $this;
    }

    /**
     * @return string
     */
    public function getUSRetailPrice()
    {
      return $this->USRetailPrice;
    }

    /**
     * @param string $USRetailPrice
     * @return Set
     */
    public function setUSRetailPrice($USRetailPrice)
    {
      $this->USRetailPrice = $USRetailPrice;
      return $this;
    }

    /**
     * @return string
     */
    public function getCARetailPrice()
    {
      return $this->CARetailPrice;
    }

    /**
     * @param string $CARetailPrice
     * @return Set
     */
    public function setCARetailPrice($CARetailPrice)
    {
      $this->CARetailPrice = $CARetailPrice;
      return $this;
    }

    /**
     * @return string
     */
    public function getEURetailPrice()
    {
      return $this->EURetailPrice;
    }

    /**
     * @param string $EURetailPrice
     * @return Set
     */
    public function setEURetailPrice($EURetailPrice)
    {
      $this->EURetailPrice = $EURetailPrice;
      return $this;
    }

    /**
     * @return string
     */
    public function getUSDateAddedToSAH()
    {
      return $this->USDateAddedToSAH;
    }

    /**
     * @param string $USDateAddedToSAH
     * @return Set
     */
    public function setUSDateAddedToSAH($USDateAddedToSAH)
    {
      $this->USDateAddedToSAH = $USDateAddedToSAH;
      return $this;
    }

    /**
     * @return string
     */
    public function getUSDateRemovedFromSAH()
    {
      return $this->USDateRemovedFromSAH;
    }

    /**
     * @param string $USDateRemovedFromSAH
     * @return Set
     */
    public function setUSDateRemovedFromSAH($USDateRemovedFromSAH)
    {
      $this->USDateRemovedFromSAH = $USDateRemovedFromSAH;
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
      } else {
        try {
          return new \DateTime($this->lastUpdated);
        } catch (\Exception $e) {
          return null;
        }
      }
    }

    /**
     * @param \DateTime $lastUpdated
     * @return Set
     */
    public function setLastUpdated(\DateTime $lastUpdated)
    {
      $this->lastUpdated = $lastUpdated->format(\DateTime::ATOM);
      return $this;
    }

}
