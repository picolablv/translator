<?php
namespace Picolab\Translator\Providers;


use GuzzleHttp\Client;

interface ProviderInterface
{

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function setParam($key, $value);

    /**
     * @return mixed
     */
    public function getTranslated();

    /**
     * @param Client $guzzleClient
     * @return mixed
     */
    public function makeRequest(Client $guzzleClient);

    /**
     * @return mixed
     */
    public function __toString();

    /**
     * @return mixed
     */
    public function getResponse();

    /**
     * @param $requestUrl
     * @param string $method
     * @param array $data
     * @return mixed
     */
    public function send($requestUrl, $method = 'GET', $data = array());
}