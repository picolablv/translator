<?php
namespace Picolab\Translator\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class LetsMTProvider implements ProviderInterface
{

    /**
     * @var
     */
    protected $apiUrl = 'https://www.letsmt.eu/ws/service.svc/json/';


    protected $systemData;

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
     * LetsMTProvider constructor.
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

        $query = ['systemID' => $this->params['systemID'],];

        // get some systemInfo
        if (empty($this->systemData)) {
            $this->getSystemInfo();
        }

        try {

            $requestUrl = $this->apiUrl . 'Translate';
            $query['text'] = $this->params['text'];

            if (is_array($this->params['text'])) {
                $requestUrl = $this->apiUrl . 'TranslateArray';
                $query['textArray'] = '["' . implode('","', $this->params['text']) . '"]';
            }

            $response = $this->send($requestUrl, 'GET', ['query' => $query]);

            if ($response) {
                $this->response['code'] = $response->getStatusCode();
                if (is_array($this->params['text'])) {
                    $returnArray = $response->getBody()->getContents();
                    $this->response['text'] = json_decode($returnArray, true);
                } else {
                    $this->response['text'][] = $response->getBody()->getContents();
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
        $data = array_merge_recursive($data, ['headers' => ['client-id' => $this->params['client_id']]]);

        return $this->client->request($method, $requestUrl, $data);
    }


    /**
     * @return mixed
     */
    public function getTranslated()
    {
        if (isset($this->response['text'])) {

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


    /**
     * @return $this
     */
    private function getSystemInfo()
    {

        $response = $this->send($this->apiUrl . 'GetSystemList', 'GET', []);

        $this->systemData = json_decode($response->getBody()->getContents());
        foreach ($this->systemData->System as $system) {
            if ($system->ID == $this->params['systemID']) {
                foreach ($system->Metadata as $metadata) {
                    if ($metadata->Key == 'srclang') {
                        $this->response['from'] = $metadata->Value;
                    }
                    if ($metadata->Key == 'trglang') {
                        $this->response['to'] = $metadata->Value;
                    }
                }
                continue;
            }
        }

        return $this;
    }
}
