<?php
namespace Picolab\Translator;


use Picolab\Translator\Providers\ProviderInterface;
use GuzzleHttp\Client;

class Translator
{

    /**
     * @var
     */
    protected $provider;

    /**
     * @var null
     */
    protected $guzzleClientInstance = null;


    /**
     * @param ProviderInterface $provider
     * @return $this
     */
    public function setProvider(ProviderInterface $provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @param string $lang
     * @return $this
     */
    public function from($lang = null)
    {
        $this->provider->setParam('source', $lang);

        return $this;
    }

    /**
     * @param string $lang
     * @return $this
     */
    public function to($lang)
    {
        $this->provider->setParam('target', $lang);

        return $this;
    }

    /**
     * @param string $format
     * @return $this
     */
    public function format($format = 'text')
    {
        $this->provider->setParam('format', $format);

        return $this;
    }


    /**
     * @param $string
     * @return mixed
     */
    public function translate($string)
    {
        $this->provider->setParam('text', trim($string));

        if ($this->guzzleClientInstance == null) {
            $guzzleClientInstance = new  Client();
            $this->setGuzzleInstance($guzzleClientInstance);
        }

        $response = $this->provider->makeRequest($this->guzzleClientInstance);

        return $response;
    }


    /**
     * @param Client $guzzleClientInstance
     * @return $this
     */
    public function setGuzzleInstance(Client $guzzleClientInstance)
    {

        $this->guzzleClientInstance = $guzzleClientInstance;

        return $this;
    }
}
