<?php
namespace Picolab\Translator\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class YandexProvider implements ProviderInterface
{

    /**
     * @var
     */
    protected $apiUrl = 'https://translate.yandex.net/api/v1.5/tr.json/';


    /**
     * @var
     */
    protected $params;


    /**
     * @var
     */
    protected $response;

    /**
     * @var
     */
    protected $client;


    /**
     * YandexProvider constructor.
     *
     * @param null $config
     */
    public function __construct($config = null)
    {
        $this->params = $config;
    }


    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }


    /**
     * @param Client $guzzleClientInstance
     * @return bool|mixed
     */
    public function makeRequest(Client $guzzleClientInstance)
    {
        $this->client = $guzzleClientInstance;

        $query = ['key' => $this->params['key'], 'lang' => $this->params['target'],];

        if (isset($this->params['source'])) {
            $query['lang'] = $this->params['source'] . '-' . $this->params['target'];
        }

        $query['text'] = $this->params['text'];

        $requestUrl = $this->apiUrl . 'translate';

        if (is_array($this->params['text'])) {
            $query['text'] = json_encode($this->params['text']);
        }

        try {

            $response = $this->send($requestUrl, 'POST', ['query' => $query]);
            if ($response) {
                $this->response['code'] = $response->getStatusCode();
                $resp = json_decode($response->getBody()->getContents(), true);
                if (isset($resp['lang'])) {
                    list($this->response['from'], $this->response['to']) = explode('-', $resp['lang']);
                }

                $this->response['text'] = $resp['text'][0];
                if (is_array($this->params['text'])) {
                    $this->response['text'] = json_decode($resp['text'][0], true);
                }
            }

            return $this;

        } catch (ClientException $e) {
            $this->response['code'] = $e->getCode();
            $this->response['error'] = json_decode(ClientException::getResponseBodySummary($e->getResponse()), true);
        }

        return $this;
    }


    /**
     * @param $requestUrl
     * @param string $method
     * @param array $data
     * @return mixed
     */
    public function send($requestUrl, $method = 'GET', $data = array())
    {
        return $this->client->request($method, $requestUrl, $data);
    }


    /**
     * @return mixed
     */
    public function getTranslated()
    {
        if (isset($this->response['text'][0])) {

            return $this->response['text'][0];
        }

        return $this->params['text'];
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getTranslated();
    }


    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
