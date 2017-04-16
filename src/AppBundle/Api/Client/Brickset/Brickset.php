<?php

namespace AppBundle\Api\Client\Brickset;

use AppBundle\Api\Client\Brickset\Entity\AdditionalImage;
use AppBundle\Api\Client\Brickset\Entity\Instructions;
use AppBundle\Api\Client\Brickset\Entity\Review;
use AppBundle\Api\Client\Brickset\Entity\Set;
use AppBundle\Api\Client\Brickset\Entity\Subtheme;
use AppBundle\Api\Client\Brickset\Entity\Theme;
use AppBundle\Api\Client\Brickset\Entity\Year;
use AppBundle\Api\Exception\ApiException;
use AppBundle\Api\Exception\AuthenticationFailedException;
use AppBundle\Api\Exception\CallFailedException;
use AppBundle\Api\Exception\EmptyResponseException;
use Symfony\Component\Debug\Exception\ContextErrorException;

class Brickset extends \SoapClient
{
    const WSDL = 'http://brickset.com/api/v2.asmx?WSDL';

    private $apiKey = '';

    private $userHash = '';

    /**
     * @var array The defined classes
     */
    private static $classmap = [
        'sets' => Set::class,
        'additionalImages' => AdditionalImage::class,
        'instructions' => Instructions::class,
        'reviews' => Review::class,
        'themes' => Theme::class,
        'subthemes' => Subtheme::class,
        'years' => Year::class,
    ];

    /**
     * @param string $apikey  Brickset API key
     * @param array  $options A array of config values
     * @param string $wsdl    The wsdl file to use
     */
    public function __construct($apikey, $wsdl = null, array $options = [])
    {
        $this->apiKey = $apikey;

        $options['cache_wsdl'] = WSDL_CACHE_NONE;
        $options['exceptions'] = true;

        foreach (self::$classmap as $key => $value) {
            if (!isset($options['classmap'][$key])) {
                $options['classmap'][$key] = $value;
            }
        }
        if (!$wsdl) {
            $wsdl = self::WSDL;
        }

        try {
            parent::__construct($wsdl, $options);
        } catch (\Exception $exception) {
            throw new ApiException(ApiException::BRICKSET);
        }
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function call($method, $parameters)
    {
        $parameters['apiKey'] = $this->apiKey;

        try {
            $this->checkApiKey();

            return $this->__soapCall($method, [$parameters])->{$method.'Result'};
        } catch (\SoapFault $e) {
            throw new CallFailedException(ApiException::BRICKSET);
        } catch (ContextErrorException $e) {
            throw new EmptyResponseException(ApiException::BRICKSET);
        } catch (\Exception $e) {
            throw new ApiException(ApiException::BRICKSET);
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
        if (!array_key_exists('pageSize', $parameters)) {
            $parameters['pageSize'] = 1000;
        }

        // Add blank required parameters to api call in order to recieve response
        $required_keys = ['query', 'theme', 'subtheme', 'setNumber', 'year', 'owned', 'wanted', 'orderBy', 'pageSize', 'pageNumber', 'userName'];
        foreach ($required_keys as $key) {
            if (!array_key_exists($key, $parameters)) {
                $parameters[$key] = '';
            }
        }

        $response = $this->call('getSets', $parameters)->sets;

        return is_array($response) ? $response : [$this->getSet($response->getSetID())];
    }

    /**
     * @param $SetID
     *
     * @return Set
     */
    public function getSet($SetID)
    {
        $parameters = ['userHash' => $this->userHash, 'SetID' => $SetID];

        return $this->call('getSet', $parameters)->sets;
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

        $response = $this->call('getRecentlyUpdatedSets', $parameters)->sets;

        return is_array($response) ? $response : [$response];
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

        $response = $this->call('getAdditionalImages', $parameters)->additionalImages;

        return is_array($response) ? $response : [$response];
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

        $response = $this->call('getReviews', $parameters)->reviews;

        return is_array($response) ? $response : [$response];
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

        $response = $this->call('getInstructions', $parameters)->instructions;

        return is_array($response) ? $response : [$response];
    }

    /**
     * Get a list of themes, with the total number of sets in each.
     *
     * @return Theme[]
     */
    public function getThemes()
    {
        return $this->call('getThemes', [])->themes;
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

        $response = $this->call('getSubthemes', $parameters)->subthemes;

        return is_array($response) ? $response : [$response];
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

        $response = $this->call('getYears', $parameters)->years;

        return is_array($response) ? $response : [$response];
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

        $response = $this->call('login', $parameters);
        if ($response == 'INVALIDKEY') {
            return false;
        } elseif (strpos($response, 'ERROR:') === 0) {
            return false;
        }
        $this->userHash = $response;

        return true;
    }

    /**
     * Check if an API key is valid.
     *
     * @throws AuthenticationFailedException If api key is not valid
     */
    private function checkApiKey()
    {
        $parameters['apiKey'] = $this->apiKey;

        if ($this->__soapCall('checkKey', [$parameters])->checkKeyResult != 'OK') {
            throw new AuthenticationFailedException(ApiException::BRICKSET);
        }
    }
}
