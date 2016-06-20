<?php
namespace picolab\Translator\Providers;


use GuzzleHttp\Client;

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
     * @param $query
     * @return string
     */
    private function hash($query)
    {
        return $this->params['lang'] . ':' . md5(strtolower($query));
    }


    /**
     * @param Client $guzzleClient
     * @return bool
     */
    public function makeRequest(Client $guzzleClient)
    {
        $this->params['lang'] = $this->params['source'] . '-' . $this->params['target'];
        unset($this->params['source'], $this->params['target']);
        $sendUrl = $this->apiUrl . '?' . http_build_query($this->params);

        $response = $guzzleClient->request('GET', $sendUrl);

        if ($response) {
            $this->response = json_decode($response->getBody());

            return $this->getTranslated();
        }

        return false;
    }


    /**
     * @return mixed
     */
    public function getTranslated()
    {
        if ($this->response->text[0]) {
            return $this->response->text[0];
        }

        return $this->params['text'];
    }
}
