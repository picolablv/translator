<?php
namespace Picolab\Translator\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class LetsMTProvider implements ProviderInterface
{

    /**
     * @var
     */
    protected $apiUrl = 'https://www.letsmt.eu/ws/service.svc/json/Translate';


    /**
     * @var
     */
    protected $params = array('appID', 'uiLanguageID', 'systemID', 'text', 'options');


    /**
     * @var
     */
    protected $response;


    /**
     * YandexProvider constructor.
     *
     * @param null $config
     */
    public function __construct($config = null)
    {
        if (isset($config['system_id'])) {
            $this->setParam('systemID', $config['system_id']);
        }
        if (isset($config['client_id'])) {
            $this->setParam('client_id', $config['client_id']);
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


        unset($this->params['source'], $this->params['target']);
        $sendUrl = $this->apiUrl . '?' . http_build_query($this->params);

        try {
            $response = $guzzleClientInstance->request('GET', $sendUrl, ['headers' => ['client-id' => $this->params['client_id']]]);
            if ($response) {
                $this->response = json_decode($response->getBody());

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
        if (isset($this->response)) {

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
        if (!is_object($this->response)) {
            return json_decode($this->response);
        }

        return $this->response;
    }
}
