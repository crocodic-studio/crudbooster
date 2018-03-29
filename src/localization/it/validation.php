<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Il campo :attribute deve essere accettato.',
    'active_url' => 'Il campo :attribute non è un URL valido.',
    'after' => 'Il campo :attribute deve essere una data dopo :date.',
    'after_or_equal' => 'Il campo :attribute deve essere una data successiva o uguale a :date.',
    'alpha' => 'Il campo :attribute può contenere solo lettere.',
    'alpha_dash' => 'Il campo :attribute può solo contenere lettere numeri e trattini.',
    'alpha_num' => 'Il campo :attribute può solo contenere lettere e numeri.',
    'array' => 'Il campo :attribute deve essere un array.',
    'before' => 'Il campo :attribute deve essere una data precedente a :date.',
    'before_or_equal' => 'Il campo :attribute deve essere una data precedente o uguale a :date.',
    'between' => [
        'numeric' => 'Il campo :attribute deve esseere tra :min e :max.',
        'file' => 'Il campo :attribute deve essere tra :min e :max kilobytes.',
        'string' => 'Il campo :attribute deve essere tra  :min e :max caratteri.',
        'array' => 'Il campo :attribute deve avere :min a :max items.',
    ],
    'boolean' => 'Il campo :attribute campo deve essere true o false.',
    'confirmed' => 'Il campo :attribute di conferma non corrisponde.',
    'date' => 'Il campo :attribute non è una data valida.',
    'date_format' => 'Il campo :attribute non combacia con il formato :format.',
    'different' => 'Il campo :attribute e :other devono essere diversi.',
    'digits' => 'Il campo :attribute devono essere :digits cifre.',
    'digits_between' => 'Il campo :attribute devono essere tra :min e :max cifre.',
    'dimensions' => 'Il campo :attribute ha una dimensione non valida.',
    'distinct' => 'Il campo :attribute ha un valore doppio.',
    'email' => 'Il campo :attribute deve essere una mail valida.',
    'exists' => 'Il campo :attribute non è valido.',
    'file' => 'Il campo :attribute deve essere un file.',
    'filled' => 'Il campo :attribute deve avere un valore.',
    'image' => 'Il campo :attribute deve essere un immagine.',
    'in' => 'Il campo :attribute non è valido.',
    'in_array' => 'Il campo :attribute non esiste in :other.',
    'integer' => 'Il campo :attribute deve essere un intero.',
    'ip' => 'Il campo :attribute deve essere un IP address valido.',
    'ipv4' => 'Il campo :attribute deve essere un IPv4 address valido.',
    'ipv6' => 'Il campo :attribute deve essere un IPv6 address valido.',
    'json' => 'Il campo :attribute deve essere una stringa JSON valida.',
    'max' => [
        'numeric' => 'Il campo :attribute non può essere maggiore di :max.',
        'file' => 'Il campo :attribute non può essere maggiore di :max kilobytes.',
        'string' => 'Il campo :attribute non può essere maggiore di :max caratteri.',
        'array' => 'Il campo :attribute non può avere più di :max items.',
    ],
    'mimes' => 'Il campo :attribute deve essere un file del tipo: :values.',
    'mimetypes' => 'Il campo :attribute deve essere un file del tipo: :values.',
    'min' => [
        'numeric' => 'Il campo :attribute deve essere almeno :min.',
        'file' => 'Il campo :attribute deve essere almeno :min kilobytes.',
        'string' => 'Il campo :attribute deve essere almeno :min caratteri.',
        'array' => 'Il campo :attribute deve avere almeno :min items.',
    ],
    'not_in' => 'Il campo :attribute non è valido.',
    'numeric' => 'Il campo :attribute deve essere un numero.',
    'present' => 'Il campo :attribute deve essere presente.',
    'regex' => 'Il formato del campo :attribute non è valido.',
    'required' => 'Il campo :attribute è obbligatorio.',
    'required_if' => 'Il campo :attribute è obbligatorio se :other è :value.',
    'required_unless' => 'Il campo :attribute è obbligatorio a meno che :other sia tra :values.',
    'required_with' => 'Il campo :attribute è richiesto quando :values è presente.',
    'required_with_all' => 'Il campo :attribute è richiesto quando :values è presente.',
    'required_without' => 'Il campo :attribute è richiesto quando :values non è presente.',
    'required_without_all' => 'Il campo :attribute quando nessuno di :values è presente.',
    'same' => 'Il campo :attribute e :other devono corrispondere.',
    'size' => [
        'numeric' => 'Il campo :attribute deve essere :size.',
        'file' => 'Il campo :attribute deve essere :size kilobytes.',
        'string' => 'Il campo :attribute deve essere :size caratteri.',
        'array' => 'Il campo :attribute deve contenere :size items.',
    ],
    'string' => 'Il campo :attribute deve essere una stringa.',
    'timezone' => 'Il campo :attribute deve essere in una zona valida.',
    'unique' => 'Il campo :attribute è stato già selezionato.',
    'uploaded' => 'Il campo :attribute ha fallito il caricamento.',
    'url' => 'Il campo :attribute ha un formato non valido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
