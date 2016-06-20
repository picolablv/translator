<?php
namespace picolab\Translator\Providers;


use GuzzleHttp\Client;

interface ProviderInterface {
    

    
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
}