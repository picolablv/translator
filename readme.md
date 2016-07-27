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
     'api-key' => 'your api key',
 ]);
 
$translator = new Picolab\Translator\Translator();
$translator->setProvider($translateProvider);

$translation = $translator->from('en')->to('ru')->translate('Some other language');
// you can use language autodetect feature, if provider is supporting it:
// $translation = $translator->to('ru')->translate('Some other language');

 // output translation 
 echo $translation;
``` 


3. Tilde Machine Translation (Tilde MT) provider

dev docs: http://www.tilde.com/mt/tools/api

```php

$translateProvider = new Picolab\Translator\Providers\LetsMTProvider([
    'client_id' => 'client ID',
    'system_id' => 'System ID',
 ]);
 
$translator = new Picolab\Translator\Translator();
$translator->setProvider($translateProvider);

// Due to the fact that available languages is already defined in the MT system, 
// you do not need to specify them there
// Translation system EN-LV
$translation = $translator->translate('Some other language');

 // output translation 
 echo $translation;
``` 

Todo:
- [ ] Google Translate Provider
- [ ] Translating providers error handling
- [ ] Use the full capabilities of the providers translating system ( translate array, etc...)

License: MIT