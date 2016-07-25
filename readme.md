Simple translator class with some translate providers

1. Bing ( Microsoft Translator service) provider

dev docs: https://msdn.microsoft.com/en-us/library/dd576287.aspx

```php
 $translateProvider = new Picolab\Translator\Providers\BingProvider([
     'client_id' => 'client id',
     'client_secret' => 'client secret',
 ]);
 
 $translator = new Picolab\Translator\Translator();
 $translator->setProvider($translateProvider);
 $translation = $translator->from('en')->to('es')->translate('Some other language');
 // you can use language autodetect feature, if provider is supporting it:
 // $translation = $translator->to('es')->translate('Some other language');
 
 // output translation 
 echo $translation;
    
```
 
2. Yandex provider

dev docs: https://translate.yandex.com/developers

```php

$translateProvider = new Picolab\Translator\Providers\YandexProvider([
     'apikey' => 'your api key',
 ]);
 
$translator = new Picolab\Translator\Translator();
$translator->setProvider($translateProvider);

$translation = $translator->from('en')->to('ru')->translate('Some other language');
// you can use language autodetect feature, if provider is supporting it:
// $translation = $translator->to('ru')->translate('Some other language');

 // output translation 
 echo $translation;
``` 


License: MIT