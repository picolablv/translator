Very simple example laravel translate controller

 
```php


    public function __construct(Translator $translator) {


        $translateProvider = new YandexProvider([
            'api-key' => '---your-api-key---',
        ]);

        $this->translator = $translator;
        $this->translator->setProvider($translateProvider);

    }

    public function manage(Request $request)
    {
        $text = $request->input('text');
        $from = 'lv';
        $to = 'en';
         
        
        $translation = $this->translator->from($from)->to($to)->translate($text);

        return response()->json(['text' => $translation]);
    }
    
    
```
