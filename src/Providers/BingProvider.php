<?php
namespace Picolab\Translator\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Stream;

class BingProvider implements ProviderInterface
{

    /**
     * @var
     */
    protected $apiUrl = 'http://api.microsofttranslator.com/V2/Http.svc/Translate';

    /**
     * @var string
     */
    protected $authUrl = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/';

    /**
     * @var
     */
    protected $accessToken;

    /**
     * @var
     */
    protected $params = ['text' => '', 'contentType' => 'text/plain', 'category' => 'general', 'to' => ''];


    /**
     * @var
     */
    protected $response;


    /**
     * BingProvider constructor.
     *
     * @param null $config
     */
    public function __construct($config = null)
    {
        if (isset($config['client_id'])) {
            $this->setParam('client_id', $config['client_id']);
        }
        if (isset($config['client_secret'])) {
            $this->setParam('client_secret', $config['client_secret']);
        }

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
        if (empty($this->accessToken)) {
            $this->accessToken = $this->getAccessToken($guzzleClientInstance);
        }


        if (isset($this->params['source'])) {
            $this->params['from'] = $this->params['source'];
        }

        $this->params['to'] = $this->params['target'];


        unset($this->params['source'], $this->params['target'], $this->params['client_id'], $this->params['client_secret']);

        $sendUrl = $this->apiUrl . '?' . http_build_query($this->params);

        try {
            $response = $guzzleClientInstance->request('GET', $sendUrl, ['headers' => ['Authorization' => 'Bearer ' . $this->accessToken]]);

            if ($response) {
                $this->response = $response->getBody()->getContents();

                return $this;
            }
        } catch (ClientException $e) {
            $this->response = ClientException::getResponseBodySummary($e->getResponse());
        }

        return $this;
    }


    /**
     * @return mixed
     */
    public function getTranslated()
    {
        if (isset($this->response) and is_string($this->response)) {

            return $this->response;
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
        if (is_string($this->response)) {
            return $this->response;
        }

        if (!is_object($this->response)) {
            return json_decode($this->response);
        }

        return $this->response;
    }

    /**
     * @return array
     */
    public function getParams()
    {

        return $this->params;
    }

    /**
     * @param Client $guzzleClientInstance
     * @return $this
     */
    public function getAccessToken(Client $guzzleClientInstance)
    {
        $paramArr = [
            'grant_type' => 'client_credentials',
            'scope' => 'http://api.microsofttranslator.com',
            'client_id' => $this->params['client_id'],
            'client_secret' => $this->params['client_secret']
        ];

        try {
            $response = $guzzleClientInstance->post($this->authUrl, ['form_params' => $paramArr]);
            if ($response) {
                $tokenRequestProperties = json_decode($response->getBody());

                return $tokenRequestProperties->access_token;
            }

        } catch (ClientException $e) {
            $this->response = ClientException::getResponseBodySummary($e->getResponse());
        }

        return $this;
    }
}
