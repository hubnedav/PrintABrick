<?php

namespace AppBundle\Client\Brickset;

use AppBundle\Client\Brickset\Entity\AdditionalImage;
use AppBundle\Client\Brickset\Entity\Instructions;
use AppBundle\Client\Brickset\Entity\Review;
use AppBundle\Client\Brickset\Entity\Set;
use Symfony\Component\Asset\Exception\LogicException;
use Symfony\Component\Debug\Exception\ContextErrorException;

class Brickset extends \SoapClient
{
	const WSDL = 'http://brickset.com/api/v2.asmx?WSDL';

    private $apiKey = '';
    private $userHash = '';

    /**
     * @var array The defined classes
     */
    private static $classmap = array(
        'sets' => Set::class,
        'additionalImages' => AdditionalImage::class,
        'instructions' => Instructions::class,
        'reviews' => Review::class,
    );

    /**
     * @param string $apikey  Brickset API key
     * @param array  $options A array of config values
     * @param string $wsdl    The wsdl file to use
     */
    public function __construct($apikey, $wsdl = null, array $options = array())
    {
        $this->apiKey = $apikey;

        foreach (self::$classmap as $key => $value) {
            if (!isset($options['classmap'][$key])) {
                $options['classmap'][$key] = $value;
            }
        }
        if (!$wsdl) {
            $wsdl = self::WSDL;
        }
        parent::__construct($wsdl, $options);
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param $query
     * @param $theme
     * @param $subtheme
     * @param $setNumber
     * @param $year
     * @param $owned
     * @param $wanted
     * @param $orderBy
     * @param $pageSize
     * @param $pageNumber
     * @param $userName
     *
     * @return Set[]
     */
    public function getSets($query, $theme, $subtheme, $setNumber, $year, $owned, $wanted, $orderBy, $pageSize, $pageNumber, $userName)
    {
        $parameters = [
            'apiKey' => $this->apiKey,
            'userHash' => $this->userHash,
            'query' => $query,
            'theme' => $theme,
            'subtheme' => $subtheme,
            'setNumber' => $setNumber,
            'year' => $year,
            'owned' => $owned,
            'wanted' => $wanted,
            'orderBy' => $orderBy,
            'pageSize' => $pageSize,
            'pageNumber' => $pageNumber,
            'userName' => $userName,
        ];

        try {
            return $this->__soapCall('getSets', [$parameters])->getSetsResult->sets;
        } catch (ContextErrorException $e) {
            return [];
        } catch (\SoapFault $e) {
            //TODO
            throw new LogicException($e->getCode().' - '.$e->getMessage());
        }
    }

    /**
     * @param $SetID
     *
     * @return Set
     */
    public function getSet($SetID)
    {
        $parameters = [
            'apiKey' => $this->apiKey,
            'userHash' => $this->userHash,
            'SetID' => $SetID,
        ];

        try {
            return $this->__soapCall('getSet', [$parameters])->getSetResult->sets[0];
        } catch (ContextErrorException $e) {
            return null;
        } catch (\SoapFault $e) {
            //TODO
            throw new LogicException($e->getCode().' - '.$e->getMessage());
        }
    }

    /**
     * @param int $minutesAgo
     *
     * @return Set[]
     */
    public function getRecentlyUpdatedSets($minutesAgo)
    {
        $parameters = [
            'apiKey' => $this->apiKey,
            'minutesAgo' => $minutesAgo,
        ];

        try {
            return $this->__soapCall('getRecentlyUpdatedSets', [$parameters])->getRecentlyUpdatedSetsResult->sets;
        } catch (ContextErrorException $e) {
            return [];
        } catch (\SoapFault $e) {
            //TODO
            throw new LogicException($e->getCode().' - '.$e->getMessage());
        }
    }

    /**
     * @param $setID
     *
     * @return AdditionalImage[]
     */
    public function getAdditionalImages($setID)
    {
        $parameters = [
            'apiKey' => $this->apiKey,
            'setID' => $setID,
        ];

        try {
            return $this->__soapCall('getAdditionalImages', [$parameters])->getAdditionalImagesResult->additionalImages;
        } catch (ContextErrorException $e) {
            return [];
        } catch (\SoapFault $e) {
            //TODO
            throw new LogicException($e->getCode().' - '.$e->getMessage());
        }
    }

    public function getReviews($setID)
    {
        $parameters = [
            'apiKey' => $this->apiKey,
            'setID' => $setID,
        ];

        try {
            return $this->__soapCall('getReviews', [$parameters])->getReviewsResult->reviews;
        } catch (ContextErrorException $e) {
            return [];
        } catch (\SoapFault $e) {
            //TODO
            throw new LogicException($e->getCode().' - '.$e->getMessage());
        }
    }

    public function getInstructions($setID)
    {
        $parameters = [
            'apiKey' => $this->apiKey,
            'setID' => $setID,
        ];

        try {
            return $this->__soapCall('getInstructions', [$parameters])->getInstructionsResult->instructions;
        } catch (ContextErrorException $e) {
            return [];
        } catch (\SoapFault $e) {
            //TODO
            throw new LogicException($e->getCode().' - '.$e->getMessage());
        }
    }

    public function login($username, $password)
    {
        $parameters = [
            'apiKey' => $this->apiKey,
            'username' => $username,
            'password' => $password,
        ];

        try {
            $response = $this->__soapCall('login', [$parameters])->loginResult;
            if ($response == 'INVALIDKEY') {
                return false;
            } elseif (strpos($response, 'ERROR:') === 0) {
                return false;
            } else {
                $this->userHash = $response;

                return true;
            }
        } catch (\SoapFault $e) {
            //TODO
            throw new LogicException($e->getCode().' - '.$e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function checkKey()
    {
        $parameters = [
            'apiKey' => $this->apiKey,
        ];

        try {
            return ($this->__soapCall('checkKey', [$parameters])->checkKeyResult) == 'OK' ? true : false;
        } catch (\SoapFault $e) {
            //TODO
            throw new LogicException($e->getCode().' - '.$e->getMessage());
        }
    }
}
