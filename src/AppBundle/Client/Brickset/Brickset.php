<?php

namespace AppBundle\Client\Brickset;

use AppBundle\Client\Brickset\Entity\AdditionalImage;
use AppBundle\Client\Brickset\Entity\Instructions;
use AppBundle\Client\Brickset\Entity\Review;
use AppBundle\Client\Brickset\Entity\Set;
use AppBundle\Client\Brickset\Entity\Subtheme;
use AppBundle\Client\Brickset\Entity\Theme;
use AppBundle\Client\Brickset\Entity\Year;
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
        'themes' => Theme::class,
        'subthemes' => Subtheme::class,
        'years' => Year::class,
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

    private function call($method, $parameters)
    {
        $parameters['apiKey'] = $this->apiKey;

        try {
            return $this->__soapCall($method, [$parameters]);
        } catch (\SoapFault $e) {
            //TODO
            throw new LogicException($e->getCode().' - '.$e->getMessage());
        }
    }

    /**
     * Retrieve a list of sets.
     *
     * @param array $parameters
     *
     * @return Set[]
     */
    public function getSets($parameters)
    {
        $parameters['userHash'] = $this->userHash;

        // Add blank required parameters to api call in order to recieve response
        $required_keys = ['query', 'theme', 'subtheme', 'setNumber', 'year', 'owned', 'wanted', 'orderBy', 'pageSize', 'pageNumber', 'userName'];
        foreach ($required_keys as $key) {
            if (!array_key_exists($key, $parameters)) {
                $parameters[$key] = '';
            }
        }

        try {
            $response = $this->call('getSets', $parameters)->getSetsResult->sets;

            return is_array($response) ? $response : [$response];
        } catch (ContextErrorException $e) {
            return [];
        }
    }

    /**
     * @param $SetID
     *
     * @return Set
     */
    public function getSet($SetID)
    {
        $parameters = ['userHash' => $this->userHash, 'SetID' => $SetID];

        try {
            return $this->call('getSet', $parameters)->getSetResult->sets;
        } catch (ContextErrorException $e) {
            return null;
        }
    }

    /**
     * Get a list of sets that have changed in the last {minutesAgo} minutes.
     *
     * @param int $minutesAgo
     *
     * @return Set[]
     */
    public function getRecentlyUpdatedSets($minutesAgo)
    {
        $parameters = ['minutesAgo' => $minutesAgo];

        try {
            $response = $this->call('getRecentlyUpdatedSets', $parameters)->getRecentlyUpdatedSetsResult->sets;

            return is_array($response) ? $response : [$response];
        } catch (ContextErrorException $e) {
            return [];
        }
    }

    /**
     * Get a list of URLs of additional set images for the specified set.
     *
     * @param int $setID Brickset unique id of set
     *
     * @return AdditionalImage[]
     */
    public function getAdditionalImages($setID)
    {
        $parameters = ['setID' => $setID];

        try {
            $response = $this->call('getAdditionalImages', $parameters)->getAdditionalImagesResult->additionalImages;

            return is_array($response) ? $response : [$response];
        } catch (ContextErrorException $e) {
            return [];
        }
    }

    /**
     * Get the reviews for the specified set.
     *
     * @param int $setID Brickset unique id of set
     *
     * @return Review[]
     */
    public function getReviews($setID)
    {
        $parameters = ['setID' => $setID];

        try {
            $response = $this->call('getReviews', $parameters)->getReviewsResult->reviews;

            return is_array($response) ? $response : [$response];
        } catch (ContextErrorException $e) {
            return [];
        }
    }

    /**
     * Get a list of instructions for the specified set.
     *
     * @param int $setID Brickset unique id of set
     *
     * @return Instructions[]
     */
    public function getInstructions($setID)
    {
        $parameters = ['setID' => $setID];

        try {
            $response = $this->call('getInstructions', $parameters)->getInstructionsResult->instructions;

            return is_array($response) ? $response : [$response];
        } catch (ContextErrorException $e) {
            return null;
        }
    }

    /**
     * Get a list of themes, with the total number of sets in each.
     *
     * @return Theme[]
     */
    public function getThemes()
    {
        try {
            return $this->call('getThemes', [])->getThemesResult->themes;
        } catch (ContextErrorException $e) {
            return [];
        }
    }

    /**
     * Get a list of subthemes for a given theme, with the total number of sets in each.
     *
     * @param string $theme Name of theme
     *
     * @return Subtheme[]
     */
    public function getSubthemes($theme)
    {
        $parameters = ['theme' => $theme];

        try {
            $response = $this->call('getSubthemes', $parameters)->getSubthemesResult->subthemes;

            return is_array($response) ? $response : [$response];
        } catch (ContextErrorException $e) {
            return [];
        }
    }

    /**
     * Get a list of years for a given theme, with the total number of sets in each.
     *
     * @param string $theme Name of theme
     *
     * @return Year[]
     */
    public function getYears($theme)
    {
        $parameters = ['theme' => $theme];

        try {
            $response = $this->call('getYears', $parameters)->getYearsResult->years;

            return is_array($response) ? $response : [$response];
        } catch (ContextErrorException $e) {
            return [];
        }
    }

    /**
     * Log in as a user and retrieve a token that can be used in subsequent API calls.
     *
     * @param string $username
     * @param string $password
     *
     * @return bool True if successful
     */
    public function login($username, $password)
    {
        $parameters = ['username' => $username, 'password' => $password];

        $response = $this->call('login', $parameters)->loginResult;
        if ($response == 'INVALIDKEY') {
            return false;
        } elseif (strpos($response, 'ERROR:') === 0) {
            return false;
        } else {
            $this->userHash = $response;

            return true;
        }
    }

    /**
     * Check if an API key is valid.
     *
     * @return bool
     */
    public function checkKey()
    {
        return ($this->call('checkKey', [])->checkKeyResult) == 'OK' ? true : false;
    }
}
