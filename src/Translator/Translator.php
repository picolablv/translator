<?php
namespace picolab\Translator;

 
use picolab\Translator\Providers\ProviderInterface;

class Translator {

    /**
     * @var
     */
    protected $provider;


    protected $clientInstance;

    /**
     * Cache instance
     * @var
     */
     protected $cacheInstance;

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
        
        // todo: caching
        $this->clientInstance = new \GuzzleHttp\Client();

        $response = $this->provider->makeRequest($this->clientInstance);

        return $response;
    }
}
