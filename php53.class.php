<?php

/**
 * To use with websites on PHP < 5.3
 *
 * Dont use this... update php
 *
 * Class EazymatchClient
 */
class Eazymatch53Client
{
    //token
    var $apiToken;

    //customer slug
    var $customer;

    //API key, get one at eazymatch-online.nl
    var $apiKey;
    var $apiSecret;
    var $root;

    /**
     * EazymatchClient constructor.
     *
     * @param null $apiKey
     * @param null $apiSecret
     * @param null $customer
     * @param null $root
     * @param array $reset
     */
    public function __construct($apikey = '', $apiSecret = '', $customer = null, $root = null, $reset = true)
    {
        if (!$apikey) die('You must provide a Eazymatch API key');
        if (!$apiSecret) die('You must provide a Eazymatch API secret');
        if (!$customer) die('You must provide a Eazymatch customer slug');
        if (!$root) {
            $root = 'https://core.eazymatch.net/v1/';
        }

        //setup variables
        $this->apiKey = $apikey;
        $this->root = $root;
        $this->apiSecret = $apiSecret;
        $this->instance = $customer;

        $this->root = rtrim($this->root, '/') . '/';

        //setup connection
        if (!$reset) {
            $this->setToken($apikey);
        } else {
            $this->resetKey();
        }

    }

    /**
     * when a token is returned we need to hash this and keep it for further calls
     *
     */
    public function resetKey()
    {
        $tempToken = $this->call('session/getToken');
        $this->setToken($tempToken['result']);
    }

    /**
     * Update token?
     * @param $token
     */
    public function setToken($token)
    {
        $this->apiToken = hash('sha256', $token . $this->apiSecret);
    }

    /**
     * Post request to Eazymatch.net everything is POST there
     *
     * @param $endpoint
     * @param array $params
     * @return mixed
     */
    public function call($endpoint, $params = array())
    {

        // collect post variables for service
        $fieldData = array();
        if (!empty($params)) {
            $argumentCounter = -1;
            foreach ($params as $argument) {
                $argumentCounter++;
                $fieldData['argument[' . $argumentCounter . ']'] = json_encode($argument);
            }
        }


        $fieldData['instance'] = $this->instance;
        if (!is_null($this->apiToken)) {
            $fieldData['key'] = $this->apiToken;
        } else {
            $fieldData['key'] = $this->apiKey;
        }

        // compile url for apicall
        $url = $this->root . $endpoint . '.json';

        //open connection
        $ch = curl_init();

        // configure curl connection for api call
        curl_setopt_array($ch, array(
            // url for api call
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYPEER => false,

            // add post functionality
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($fieldData),

            // optimize connection
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 400,

            // force returning the result into an variable
            CURLOPT_RETURNTRANSFER => true
        ));

        // execute the api call
        $apiResponse = curl_exec($ch);

        $body = json_decode($apiResponse, true);

        return $body;
    }
}
