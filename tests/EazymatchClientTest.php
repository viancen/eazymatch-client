<?php
// run  ./vendor/bin/phpunit tests/EazymatchClientTest
namespace tests\Test;

class theTest extends \PHPUnit_Framework_TestCase
{
    public function testConnect()
    {

        $foo = true;
        //try and connect

        $key = 'YourKey';
        $secret = 'YourSecret';

        $tt = new \EazymatchClient(
            $key,
            $secret,
            'klantnaam',
            'https://core.eazymatch.net'
        );

        if (!empty($tt)) {
            $test = $tt->loginUser('user@eazymatch.com', 'test');
        } else {
            $this->assertTrue(false);
        }

        //session token is now set. in production = store this in local storage for future requests
        $data = $tt->get('users');

        if (!empty($data)) {
            print_r($data);
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false);
        }

    }

}