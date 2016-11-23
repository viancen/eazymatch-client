<?php
use GuzzleHttp\Client;

require_once 'EazymatchClient/Exceptions.php';

class EazymatchClient
{
    //private client (Guzzle)
    private $client;

    //customer slug
    public $customer;

    //API key, get one at eazymatch-online.nl
    public $apiKey;
    public $apiSecret;

    //What part of Eazymatch is used
    public $settings = [];

    //Root url of API
    public $root;

    //not used yet
    public $debug = false;

    //Todo: map all errors into comprehensible messages
    public static $error_map = [
        "Invalid_Key" => "Eazymatch_Invalid_Key",
    ];

    /**
     * EazymatchClient constructor.
     *
     * @param null $apiKey
     * @param null $apiSecret
     * @param null $customer
     * @param null $root
     * @param array $options
     */
    public function __construct($apikey = '', $apiSecret = '', $customer = null, $root = null, $options = [])
    {
        if (!$apikey) throw new Eazymatch_Error('You must provide a Eazymatch API key');
        if (!$apiSecret) throw new Eazymatch_Error('You must provide a Eazymatch API secret');
        if (!$customer) throw new Eazymatch_Error('You must provide a Eazymatch customer slug');
        if (!$root) {
            $root = 'https://api.eazymatch.net/v1/';
        }

        $this->apiKey = sha1($apikey . $apiSecret);
        $this->root = $root;
        $this->apiSecret = $apiSecret;
        $this->customer = $customer;

        if (!empty($options)) {
            if (!empty($options['settings'])) {
                $this->settings = $options['settings'];
            }
        }

        $this->client = new Client();

        $this->root = rtrim($this->root, '/') . '/';

    }

    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param null $email
     * @param null $passWord
     * @throws Eazymatch_Error
     * @return array $sessionTokenToReplaceApiTokenWith
     */
    public function loginUser($email = null, $passWord = null)
    {
        if (!$email) throw new Eazymatch_Error('You must provide a emailaddress');
        if (!$passWord) throw new Eazymatch_Error('You must provide a password');

        $data = $this->post('users/login', [
            'email' => $email,
            'password' => $passWord
        ]);

        if (!empty($data['session'])) {
            $this->apiKey = sha1($data['session']['token'] . $this->apiSecret);
            return $data['session'];
        } else {
            throw new Eazymatch_Error('Invalid credentials');
        }

    }

    /**
     * Post request to Eazymatch.io
     *
     * @param $endpoint
     * @param array $params
     * @return mixed
     */
    public function post($endpoint, $params = [])
    {

        try {
            $response = $this->client->request('POST', $this->root . $endpoint, [
                'headers' => [
                    'X-Authorization' => $this->apiKey,
                    'X-Customer' => $this->customer,
                    'X-response-type' => 'json',
                    'Content-Type' => 'application/json',
                ],
                'decode_content' => true,
                'verify' => false,
                'body' => json_encode($params)
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

    /**
     * Post request to Eazymatch.io
     *
     * @param $endpoint
     * @return mixed
     */
    public function get($endpoint)
    {

        try {
            $response = $this->client->request('GET', $this->root . $endpoint, [
                'headers' => [
                    'X-Authorization' => $this->apiKey,
                    'X-Customer' => $this->customer,
                    'X-response-type' => 'json',
                    'Content-Type' => 'application/json',
                ],
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

    /**
     * Put request to Eazymatch.io
     *
     * @param $endpoint
     * @return mixed
     */
    public function put($endpoint)
    {

        try {
            $response = $this->client->request('PUT', $this->root . $endpoint, [
                'headers' => [
                    'X-Authorization' => $this->apiKey,
                    'X-Customer' => $this->customer,
                    'X-response-type' => 'json',
                    'Content-Type' => 'application/json',
                ],
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

    /**
     * Put request to Eazymatch.io
     *
     * @param $endpoint
     * @return mixed
     */
    public function del($endpoint)
    {

        try {
            $response = $this->client->request('DELETE', $this->root . $endpoint, [
                'headers' => [
                    'X-Authorization' => $this->apiKey,
                    'X-Customer' => $this->customer,
                    'X-response-type' => 'json',
                    'Content-Type' => 'application/json',
                ],
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