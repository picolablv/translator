<?php
namespace Picolab\Translator\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class GoogleProvider implements ProviderInterface
{

    /**
     * @var
     */
    protected $apiUrl = 'https://www.googleapis.com/language/translate/v2';

    /**
     * @var
     */
    protected $params;

    /**
     * @var
     */
    protected $client;

    /**
     * @var
     */
    protected $response;


    /**
     * GoogleProvider constructor.
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

        $query = [
            'key' => $this->params['api_key'],
            'format' => 'text',
            'q' => $this->params['text'],
            'target' => $this->params['target'],
        ];

        if (is_array($this->params['text'])) {
            $query['q'] = json_encode($this->params['text']);
        }

        if(isset($this->params['source'])) {
            $query['source'] = $this->params['source'];
        }

        $requestUrl = $this->apiUrl;

        try {

            //print_r($query);
            //exit(PHP_EOL.' ---- ');
            $response = $this->send($requestUrl, 'GET', ['query' => $query]);

            if ($response) {
                $this->response['code'] = $response->getStatusCode();
                $this->response['to'] = $this->params['target'];

                $resp = json_decode($response->getBody()->getContents(), true);
                $this->response['from'] = (isset($this->params['source'])) ? $this->params['source'] : '';
                if (empty($this->params['source'])) {
                    $this->response['from'] = $resp['data']['translations'][0]['detectedSourceLanguage'];
                }
                $this->response['text'][] = $resp['data']['translations'][0]['translatedText'];
                if (is_array($this->params['text'])) {
                    $this->response['text'] = json_decode($resp['data']['translations'][0]['translatedText'], true);
                }

                return $this;
            }

        } catch (ClientException $e) {

            $this->response['code'] = $e->getCode();
            $this->response['error'] = ClientException::getResponseBodySummary($e->getResponse());
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
