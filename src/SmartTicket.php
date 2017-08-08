<?php

namespace smartshanghai\smartticketphp;

use GuzzleHttp\Client;

class SmartTicket {
    private $apiKey;
    private $baseUri;

    public function __construct($apiKey, $baseUri = 'https://www.smartticket.cn/api') {
        $this->apiKey = $apiKey;
        $this->baseUri = $baseUri;
    }

    /**
     * @param $verificationClientId
     * @param $verificationClientPassword
     * @param $electronicTicketToken
     * @param $electronicTicketSecret
     * @return Response
     */
    public function validateElectronicTicket($electronicTicketToken, $electronicTicketSecret, $verificationClientId, $verificationClientPassword ) {
        $options = [
            'base_uri' => $this->baseUri,
            'headers' => [
                'X-AUTH' => $verificationClientId.':'.$verificationClientPassword
            ],
            'form_params' => [
                'key' => $this->apiKey
            ]
        ];

        $client = new Client($options);

        $options = [
            'form_params' => [
                'token' => $electronicTicketToken,
                'secret' => $electronicTicketSecret
            ]
        ];

        $response = $client->request('POST', '/verify/tickets', $options);

        $response = $this->parseResponse($response);

        return $response;
    }

    /**
     * @param $electronicTicketToken string
     * @param $electronicTicketSecret string
     * @param $eventDateId int
     * @return Response
     */
    public function getElectronicTicket($electronicTicketToken, $electronicTicketSecret, $eventDateId) {
        $options = [
            'base_uri' => $this->baseUri,
            'headers' => [
                'X-KEY' => $this->apiKey
            ],
            'form_params' => [
                'secret' => $electronicTicketSecret,
                'show_date_id' => $eventDateId
            ]
        ];

        $client = new Client($options);

        $response = $client->request('GET', '/tickets/'.$electronicTicketToken);

        $response = $this->parseResponse($response);

        return $response;
    }

    /**
     * @param $electronicTicketToken string
     * @param $electronicTicketSecret string
     * @param $eventDateId int
     * @return bool|null True, false, or null if ticket not found.
     */
    public function electronicTicketIsVerified($electronicTicketToken, $electronicTicketSecret, $eventDateId) {
        $response = $this->getElectronicTicket($electronicTicketToken, $electronicTicketSecret, $eventDateId);

        if(!$response->wasSuccessful() || empty($response->getData()))
            return null;

        $data = $response->getData();

        if(empty($data['verified_on']))
            return false;

        return true;
    }

    /**
     * @param $guzzleResponse \GuzzleHttp\Psr7\Response
     * @return mixed
     */
    private function parseResponse($guzzleResponse) {
        $body = $guzzleResponse->getBody();
        $body = json_decode($body);

        $response = new Response($body['isSuccessful'], $body['message'], $body['data'], $body['executionTime']);

        return $response;
    }

    public function getEvents($params) {
        $options = [
            'base_uri' => $this->baseUri,
            'form_params' => [
                'key' => $this->apiKey
            ]
        ];

        if(empty($params['limit']))
            $params['limit'] = 10;

        $options['form_params'] = array_merge($options['form_params'], $params);

        $client = new Client($options);

        $response = $client->request('GET', '/shows');

        $response = $this->parseResponse($response);

        return $response;
    }
}