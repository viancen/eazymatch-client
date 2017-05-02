<?php
use GuzzleHttp\Client;

require_once 'EazymatchClient/Exceptions.php';

class EazymatchClient
{
    //private client (Guzzle)
    private $client;
    private $apiToken;

    //customer slug
    protected $customer;

    //API key, get one at eazymatch-online.nl
    protected $apiKey;
    protected $apiSecret;
    protected $root;

    /**
     * EazymatchClient constructor.
     *
     * @param null $apiKey
     * @param null $apiSecret
     * @param null $customer
     * @param null $root
     * @param array $options
     */
    public function __construct($apikey = '', $apiSecret = '', $customer = null, $root = null, $resetKey=true)
    {
        if (!$apikey) throw new Eazymatch_Error('You must provide a Eazymatch API key');
        if (!$apiSecret) throw new Eazymatch_Error('You must provide a Eazymatch API secret');
        if (!$customer) throw new Eazymatch_Error('You must provide a Eazymatch customer slug');
        if (!$root) {
            $root = 'https://core.eazymatch.net/v1/';
        }

        $this->apiKey = $apikey;
        $this->root = $root;
        $this->apiSecret = $apiSecret;
        $this->instance = $customer;
        $this->client = new Client();
        $this->root = rtrim($this->root, '/') . '/';

        //setup connection
        if($resetKey === true){
            $this->resetKey();
        } else {
            $this->setToken($apikey);
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

    public function setToken($token)
    {
        $this->apiToken = hash('sha256', $token . $this->apiSecret);
    }

    public function getToken()
    {
        return $this->apiToken;
    }

    /**
     * Post request to Eazymatch
     *
     * @param $endpoint
     * @param array $params
     * @return mixed
     */
    public function call($endpoint, $params = [])
    {
        try {
            // collect post variables for service
            $fieldData = [];
            if (!empty($params)) {
                $argumentCounter = -1;
                foreach ($params as $argument) {
                    $argumentCounter++;
                    $fieldData['argument[' . $argumentCounter . ']'] = $argument;
                }
            }

            $fieldData['instance'] = $this->instance;
            if (!is_null($this->apiToken)) {
                $fieldData['key'] = $this->apiToken;
            } else {
                $fieldData['key'] = $this->apiKey;
            }

            $response = $this->client->request('POST', $this->root . $endpoint . '.json', [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'X-response-type' => 'json',
                ],
                'form_params' => $fieldData,
                'decode_content' => true,
                'verify' => false
            ]);

        } catch (Eazymatch_HttpError $error) {
            return [
                'code' => $error->getCode(),
                'message' => $error->getMessage()
            ];
        }

        $body = json_decode($response->getBody(), true);
        return $body;
    }

}