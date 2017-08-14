<?php

namespace smartshanghai\smartticketphp;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class SmartTicket {
    private $apiKey;
    private $baseUri;

    /**
     * SmartTicket constructor.
     * @param $apiKey string
     * @param string $baseUri string If changing the base URI, remember to terminate it with a forward slash (required by Guzzle)
     */
    public function __construct($apiKey, $baseUri = 'https://www.smartticket.cn/api/') {
        $this->apiKey = $apiKey;
        $this->baseUri = $baseUri;
    }

    /**
     * @param $verificationClientUsername string
     * @param $verificationClientPassword string
     * @param $electronicTicketToken string
     * @param $electronicTicketSecret string
     * @return Response
     */
    public function validateElectronicTicket($electronicTicketToken, $electronicTicketSecret, $verificationClientUsername, $verificationClientPassword ) {
        $options = [
            'base_uri' => $this->baseUri,
            'headers' => [
                'X-AUTH' => $verificationClientUsername.':'.$verificationClientPassword,
                'X-KEY' => $this->apiKey
            ],
        ];

        $client = new Client($options);

        $options = [
            'form_params' => [
                'token' => $electronicTicketToken,
                'secret' => $electronicTicketSecret
            ]
        ];

        $response = $client->request('POST', 'verify/tickets', $options);

        $response = $this->parseResponse($response);

        return $response;
    }

    /**
     * @param $electronicTicketToken string
     * @param $electronicTicketSecret string
     * @return Response
     */
    public function getElectronicTicket($electronicTicketToken, $electronicTicketSecret) {
        $options = [
            'base_uri' => $this->baseUri,
            'headers' => [
                'X-KEY' => $this->apiKey
            ],
            'query' => [
                'secret' => $electronicTicketSecret
            ]
        ];

        $client = new Client($options);

        try {
            $response = $client->request('GET', 'tickets/' . $electronicTicketToken);
        }
        catch(ClientException $e) {
            $response = $e->getResponse();
        }

        $response = $this->parseResponse($response);

        return $response;
    }

    /**
     * @param $electronicTicketToken string
     * @param $electronicTicketSecret string
     * @return bool|null True, false, or null if ticket not found.
     */
    public function electronicTicketIsVerified($electronicTicketToken, $electronicTicketSecret) {
        $response = $this->getElectronicTicket($electronicTicketToken, $electronicTicketSecret);

        if(!$response->wasSuccessful() || empty($response->getData()))
            return null;

        $data = $response->getData();

        if(empty($data['verified_on']))
            return false;

        return true;
    }

    /**
     * @param $guzzleResponse \GuzzleHttp\Psr7\Response
     * @return Response
     * @throws \Exception
     */
    private function parseResponse($guzzleResponse) {
        $body = $guzzleResponse->getBody();
        $body = json_decode($body, true);

        $response = new Response($body['isSuccessful'], $body['message'], $body['data'], $body['executionTime']);

        return $response;
    }

    /**
     * @param array $params
     * @return Response
     */
    public function getEvents($params = []) {
        $options = [
            'base_uri' => $this->baseUri,
            'headers' => [
                'X-KEY' => $this->apiKey
            ],
            'query' => $params
        ];

        if(empty($params['limit']))
            $params['limit'] = 10;

        $options['query'] = array_merge($options['query'], $params);

        $client = new Client($options);

        $response = $client->request('GET', 'events');

        $response = $this->parseResponse($response);

        return $response;
    }
}