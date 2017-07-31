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
     * @return Client
     */
    private function initGuzzleClient($verificationClientId, $verificationClientPassword) {
        $options = [
            'base_uri' => $this->baseUri,
            'headers' => [
                'X-AUTH' => $verificationClientId.':'.$verificationClientPassword
            ],
            'form_params' => $this->apiKey
        ];

        $client = new Client($options);

        return $client;
    }

    /**
     * @param $verificationClientId
     * @param $verificationClientPassword
     * @param $electronicTicketToken
     * @param $electronicTicketSecret
     * @return Response
     */
    public function validateElectronicTicket($electronicTicketToken, $electronicTicketSecret, $verificationClientId, $verificationClientPassword ) {
        $this->errorMessage = null;
        $client = $this->initGuzzleClient($verificationClientId, $verificationClientPassword);

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
     * @param $guzzleResponse \GuzzleHttp\Psr7\Response
     * @return mixed
     */
    private function parseResponse($guzzleResponse) {
        $body = $guzzleResponse->getBody();
        $body = json_decode($body);

        $response = new Response($body['isSuccessful'], $body['message'], $body['data'], $body['executionTime']);

        return $response;
    }
}