<?php
namespace Picolab\Translator\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class YandexProvider implements ProviderInterface
{

    /**
     * @var
     */
    protected $apiUrl = 'https://translate.yandex.net/api/v1.5/tr.json/translate';


    /**
     * @var
     */
    protected $params = array('key', 'text', 'lang', 'format' => 'plain', 'options', 'source', 'target',);


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
        if (isset($config['api-key'])) {
            $this->setParam('key', $config['api-key']);
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
        if (isset($this->params['source'])) {
            $this->params['lang'] = $this->params['source'] . '-' . $this->params['target'];
        } else {
            $this->params['lang'] = $this->params['target'];
        }

        unset($this->params['source'], $this->params['target']);
        $sendUrl = $this->apiUrl . '?' . http_build_query($this->params);

        try {
            $response = $guzzleClientInstance->request('GET', $sendUrl);
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
        if (isset($this->response->text[0])) {

            return $this->response->text[0];
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
