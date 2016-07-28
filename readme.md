__Simple translator class with some translate providers__

### Usage 

```php

 $translator = new Picolab\Translator\Translator();
 
 // set up your translating provider from available providers
 $translator->setProvider($translateProvider);
 
 // translate:
 $translation = $translator->from('en')->to('ru')->translate('Some other language');
 
 // You can output the results immediately with echo
 echo $translation;
 // output:
 // Другой язык
 
 $outputResponse = $translation->getResponse();
 //output: 
 /*
 Array
 (
     [code] => 200
     [to] => ru
     [from] => en
     [text] => Array
         (
             [0] => Другой язык
         )
 
 )
 */

// You can use language autodetect feature, if provider is supporting it:
$translation = $translator->to('ru')->translate('Some other language');
// output $translation->getResponse():
/*
  Array
  (
      [code] => 200
      [to] => ru
      [from] => en
      [text] => Array
          (
              [0] => Другой язык
          ) 
  )
  */
 
 // You can even use arrays for translating multiple texts
 $translation = $translator->to('ru')->translate(['Some other language', 'Some other value']);
 // $translation->getResponse():
 /*
   Array
   (
       [code] => 200
       [to] => ru
       [from] => en
       [text] => Array
           (
               [0] => Другой язык
               [1] => другое значение
           )   
   )
   */
// p.s. in this case  echo $translation  will output only first array item   
```

Providers use Guzzle Http client, if you have specific guzzle client configuration, you can set up with setGuzzleInstance function

```php

 $translator = new Picolab\Translator\Translator();
 
 $translator->setGuzzleInstance($yourGuzzleClientInstance);
 ...
 
 ```


### Available providers

* [Microsoft Translator service provider](#microsoft)
* [Yandex provider](#yandex)
* [Tilde Machine Translation provider](#tilde)
* [Google Translate API provider](#google)


#### <a name="microsoft"></a> Microsoft Translator service provider

dev docs: [https://msdn.microsoft.com/en-us/library/dd576287.aspx](https://msdn.microsoft.com/en-us/library/dd576287.aspx)

```php
 $translateProvider = new Picolab\Translator\Providers\BingProvider([
     'client_id' => 'client id',
     'client_secret' => 'client secret',
 ]);
    
```

#### <a name="yandex"></a> Yandex provider

dev docs: https://translate.yandex.com/developers

```php

$translateProvider = new Picolab\Translator\Providers\YandexProvider([
     'key' => 'your api key',
 ]);
``` 

#### <a name="tilde"></a> Tilde Machine Translation provider

dev docs: http://www.tilde.com/mt/tools/api

Due to the fact that available languages is already defined in the MT system, you do not need to specify them there, but provider class will output system
source and target language
```php

$translateProvider = new Picolab\Translator\Providers\LetsMTProvider([
    'client_id' => 'client ID',
    'systemID' => 'System ID',
 ]);
 
$translator = new Picolab\Translator\Translator();
$translator->setProvider($translateProvider);

// Due to the fact that available languages is already defined in the MT system, 
// you do not need to specify them there
// Translation system EN-LV
$translation = $translator->translate('Some other language');
// output first translation 
echo $translation;
``` 

#### <a name="google"></a> Google Translate API provider

dev docs: https://cloud.google.com/translate/docs/

```php

$translateProvider = new Picolab\Translator\Providers\GoogleProvider([
    'api_key' => 'your api key'
]);
 
``` 




License: MIT