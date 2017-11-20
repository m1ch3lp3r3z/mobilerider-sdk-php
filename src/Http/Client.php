<?php

namespace Mr\Sdk\Http;


use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class Client extends \GuzzleHttp\Client
{
    protected function handleException(RequestException $ex)
    {

    }

    protected function encode(array $data)
    {
        $data = $data ? $data : [];

        return \json_encode($data);
    }

    protected function decode($stream)
    {
        return json_decode($stream, true);
    }

    public function getData($uri, array $params = [], array $options = [])
    {
        $response = $this->get($uri, [
                'query' => $params
            ] + $options);

        return $this->decode($response->getBody()->getContents());
    }

    public function postData($uri, array $data, array $headers = [], array $options = [])
    {
        $r = new Request('POST', $uri, $headers, $this->encode($data));

        $response = $this->send($r, $options);

        return $this->decode($response->getBody()->getContents());
    }

    public function putData($uri, array $data, array $headers = [], array $options = [])
    {
        $r = new Request('PUT', $uri, $headers, $this->encode($data));

        $response = $this->send($r, $options);

        return $this->decode($response->getBody()->getContents());
    }
}
