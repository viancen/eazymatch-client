<?php
namespace Viancen\EazymatchApi;
/**
 * connect to eazymatch
 *
 */
class Connect
{

    private $key = '';
    private $secret = '';
    private $instance = '';

    /**
     * Connect constructor, get your key and secret at eazymatch sales.
     *
     * @param $key
     * @param $secret
     * @param $instance
     */
    function __construct($key, $secret, $instance)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->instance = $instance;
    }

    /**
     * Call eazymatch
     *
     * @param $endpoint
     * @param $apikey
     * @param array $argu
     * @return mixed
     */
    function call($endpoint, $apikey, $argu = array())
    {

        //open connection
        $ch = curl_init();

        //api location
        $url = 'https://core.eazymatch.net/v1/' . $endpoint;

        // collect post variables for service
        $fields = array(
            // name of instance
            'instance' => $this->instance,

            // key to use for session
            'key' => $apikey
        );

        // add arugments for method
        if (is_array($argu)) {
            $argumentCounter = -1;

            foreach ($argu as $argument) {
                $argumentCounter++;
                $fields['argument[' . $argumentCounter . ']'] = $argument;
            }
        }

        // configure curl connection for api call
        curl_setopt_array($ch, array(

            // url for api call
            CURLOPT_URL => $url,

            // add post functionality
            CURLOPT_POST => true,

            //data
            CURLOPT_POSTFIELDS => http_build_query($fields),

            //no need for this ssl verifypeer
            CURLOPT_SSL_VERIFYPEER => false,

            // force returning the result into an variable
            CURLOPT_RETURNTRANSFER => true
        ));

        // execute the api call
        $apiResponse = curl_exec($ch);

        //close connection
        curl_close($ch);

        return $apiResponse;
    }
}