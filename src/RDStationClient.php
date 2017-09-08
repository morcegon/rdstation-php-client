<?php

namespace RD;

use Exception;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class RDStationClient
{
    private $client;
    private $token_public;
    private $token_private;
    private $base_url = "https://www.rdstation.com.br/api/";
    private $defaultIdentifier = "rdstation-php-integration";

    public function __construct($token_public = null, $token_private = null)
    {
        if (empty($token_public)) {
            throw new Exception("Public token not specified");
        }

        if (empty($token_private)) {
            throw new Exception("Private token not specified");
        }

        $this->token_public = $token_public;
        $this->token_private = $token_private;
        $this->client = new Client(['base_uri' => $this->base_url]);
    }

    public function createNewLead(string $email = null, array $data = [])
    {
        if (empty($email)) {
            throw new Exception("Please, inform at least the lead email");
        }

        $data['email'] = $email;
        $url = $this->setUrl('conversions');
        $response = $this->request($url, $data);

        if ($response == 200) {
            return true;
        }

        return false;
    }

    public function updateLead(string $email = null, array $data = [])
    {
        if (empty($email)) {
            throw new Exception("Please, inform at least the lead email");
        }

        if (empty($data)) {
            throw new Exception("The data does not be empty");
        }

        $url = $this->setUrl('leads').$email;
        $new_data['lead'] = $data;
        $new_data['auth_token'] = $this->token_private;

        $response = $this->request($url, $new_data, 'PUT');

        if ($response == 200) {
            return true;
        }

        return false;
    }

    /**
     * @param $url RD Station API URL
     * @param array $data
     * @param string $method GET, POST, PUT or DELETE
     * @return void
     */
    protected function request($uri, array $data = [], $method = 'POST')
    {
        $data['token_rdstation'] = $this->token_public;
        $form_params = (!empty($data)?$data:null);

        try {
            $response = $this->client->request($method, $uri, ['form_params' => $form_params]);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $reponse = $e->getResponse();
                return $response->getStatusCode();
            }
        }

        return $response->getStatusCode();
    }

    /**
     * Defines what URL the request will use
     *
     * @param string $type generic, leads or conversions
     * @param string $api_version
     * @return string $final_url
     */
    private function setUrl($type = 'generic', $api_version = '1.3')
    {
        switch ($type) {
            case 'generic':
                return "{$api_version}/services/{$this->token_private}/generic";
            case 'leads':
                return "{$api_version}/leads/";
            case 'conversions':
                return "{$api_version}/conversions";
        }
    }
}
