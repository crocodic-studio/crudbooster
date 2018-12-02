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

    'accepted'             => ':attribute должен быть подтвержден.',
    'active_url'           => ':attribute некорректный URL.',
    'after'                => ':attribute дата должна быть после :date.',
    'after_or_equal'       => ':attribute дата должна быть больше или равна :date.',
    'alpha'                => ':attribute поле может содержать только символы.',
    'alpha_dash'           => ':attribute поле может содержать только символы, числа и дефисы.',
    'alpha_num'            => ':attribute поле может содержать только символы и числа.',
    'array'                => ':attribute должно быть массивом.',
    'before'               => ':attribute дата должна быть меньше :date.',
    'before_or_equal'      => ':attribute дата должна быть меньше или равна :date.',
    'between'              => [
        'numeric' => ':attribute должно быть между :min и :max.',
        'file'    => ':attribute должно быть между :min и :max килобайт.',
        'string'  => ':attribute должно быть между :min и :max символов.',
        'array'   => ':attribute должно быть между :min и :max элементов.',
    ],
    'boolean'              => ':attribute поле должно быть true или false.',
    'confirmed'            => ':attribute подтверждение не совпадают.',
    'date'                 => ':attribute неправильная дата.',
    'date_format'          => ':attribute не соответствует формату :format.',
    'different'            => ':attribute и :other должны различаться.',
    'digits'               => ':attribute должно быть :digits разрядов.',
    'digits_between'       => ':attribute должно быть  между :min и :max разрядов.',
    'dimensions'           => ':attribute имеет недопустимые размеры изображения.',
    'distinct'             => ':attribute поле имеет повторяющееся значение.',
    'email'                => ':attribute должен быть действующий адрес электронной почты .',
    'exists'               => 'Выбранный :attribute не корректен.',
    'file'                 => ':attribute должен быть файлом.',
    'filled'               => ':attribute обязательно к заполнению.',
    'image'                => ':attribute должно быть картинкой.',
    'in'                   => 'Выбранный :attribute некорректен.',
    'in_array'             => ':attribute поле нет в :other.',
    'integer'              => ':attribute должно быть целым.',
    'ip'                   => ':attribute должен быть правильным IP адресом.',
    'json'                 => ':attribute должен быть правильной JSON строкой.',
    'max'                  => [
        'numeric' => ':attribute не может быть больше :max.',
        'file'    => ':attribute не может быть больше :max Кб.',
        'string'  => ':attribute не может быть больше :max символов.',
        'array'   => ':attribute не может быть больше :max элементов.',
    ],
    'mimes'                => ':attribute должен быть файл с типом: :values.',
    'mimetypes'            => ':attribute должен быть файл с типом: :values.',
    'min'                  => [
        'numeric' => ':attribute должен быть меньше :min.',
        'file'    => ':attribute должен быть меньше :min Кб.',
        'string'  => ':attribute должен быть меньше :min символов.',
        'array'   => ':attribute должен быть меньше :min элементов.',
    ],
    'not_in'               => 'Выбранный :attribute недопустимый.',
    'numeric'              => ':attribute должен быть числом.',
    'present'              => ':attribute поле должно присутствовать.',
    'regex'                => ':attribute недопустимый формат.',
    'required'             => ':attribute поле обязательное.',
    'required_if'          => ':attribute обязательное поле :other для :value.',
    'required_unless'      => ':attribute обязательное поле если :other в :values.',
    'required_with'        => ':attribute обязательное поле когда :values присутствует.',
    'required_with_all'    => ':attribute обязательное поле когда :values присутствует.',
    'required_without'     => ':attribute обязательное поле когда :values не присутствует.',
    'required_without_all' => ':attribute обязательное поле когда не присутствует в :values.',
    'same'                 => ':attribute и :other должны соответствовать.',
    'size'                 => [
        'numeric' => ':attribute должен быть :size.',
        'file'    => ':attribute должен быть :size Кб.',
        'string'  => ':attribute должен быть :size символов.',
        'array'   => ':attribute должен быть :size элементов.',
    ],
    'string'               => ':attribute должен быть строкой.',
    'timezone'             => ':attribute должен быть допустимой зоной.',
    'unique'               => ':attribute уже принят.',
    'uploaded'             => ':attribute ошибка загрузки.',
    'url'                  => ':attribute неверный формат.',

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
