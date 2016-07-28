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
    protected $apiUrl = 'http://api.microsofttranslator.com/V2/Http.svc/';

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
     * BingProvider constructor.
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

        if (empty($this->accessToken)) {
            $this->accessToken = $this->getAccessToken();
        }

        $query = [
            'from' => '',
            'to' => $this->params['target'],
            'text'=> $this->params['text'],
        ];

        if (isset($this->params['source'])) {
            $query['from'] = $this->params['source'];
        }

        try {

            if (!is_array($this->params['text'])) {
                $requestUrl = $this->apiUrl . 'Translate';
                $response = $this->send($requestUrl, 'GET', ['headers' => ['Authorization' => 'Bearer ' . $this->accessToken], 'query' => $query]);
                if ($response) {
                    $this->response['code'] = $response->getStatusCode();
                    $xmlObj = simplexml_load_string($response->getBody()->getContents());
                    $this->response['from'] = $query['from'];
                    $this->response['to'] = $query['to'];
                    $this->response['text'][] = (string)$xmlObj;

                    return $this;
                }

            } else {

                $requestUrl = $this->apiUrl . 'TranslateArray';
                // Create xml data for request
                $requestXml = "<TranslateArrayRequest><AppId/>";
                if (isset($query['from']) and !empty($query['from'])) {
                    $requestXml .= '<From>'.$query['from'].'</From>';
                }
                $requestXml .= '<Texts>';
                foreach ($query['text'] as $text) {
                    $requestXml .= '<string xmlns="http://schemas.microsoft.com/2003/10/Serialization/Arrays">' . $text . '</string>';
                }
                $requestXml .= '</Texts><To>' . $query['to'] . '</To></TranslateArrayRequest>';

                $response = $this->send($requestUrl, 'POST', ['headers' => ['Content-Type' => 'text/xml; charset=UTF-8', 'Authorization' => 'Bearer ' . $this->accessToken], 'body' => $requestXml]);

                if($response) {

                    $this->response['code'] = $response->getStatusCode();
                    $xmlObj = simplexml_load_string($response->getBody()->getContents());

                    foreach ($xmlObj->TranslateArrayResponse as $translatedArrObj) {
                        $this->response['from'] = (string)$translatedArrObj->From;
                        $this->response['to'] = $query['to'];
                        $this->response['text'][] = (string)$translatedArrObj->TranslatedText;
                    }

                    return $this;
                }

            }

        } catch (ClientException $e) {
            $this->response['code'] = $e->getCode();
            $this->response['error'] =  ClientException::getResponseBodySummary($e->getResponse());
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
     * @return array
     */
    public function getParams()
    {

        return $this->params;
    }


    /**
     * @return string|bool
     */
    private function getAccessToken()
    {
        $paramArr = [
            'grant_type' => 'client_credentials',
            'scope' => 'http://api.microsofttranslator.com',
            'client_id' => $this->params['client_id'],
            'client_secret' => $this->params['client_secret']
        ];

        try {
            $response = $this->client->request('POST', $this->authUrl, ['form_params' => $paramArr]);
            if ($response) {
                $tokenRequestProperties = json_decode($response->getBody());

                return $tokenRequestProperties->access_token;
            }

        } catch (ClientException $e) {
            $this->response = ClientException::getResponseBodySummary($e->getResponse());
        }

        return false;
    }
}
