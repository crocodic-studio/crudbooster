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

    'accepted'             => 'O :attribute deve ser aceito.',
    'active_url'           => 'O :attribute não é uma URL válida.',
    'after'                => 'O :attribute deve ser uma data depois de :date.',
    'after_or_equal'       => 'O :attribute deve ser uma data igual ou depois de :date.',
    'alpha'                => 'O :attribute deve conter apenas letras.',
    'alpha_dash'           => 'O :attribute deve conter apenas letras, números e traços.',
    'alpha_num'            => 'O :attribute deve conter apenas letras e números.',
    'array'                => 'O :attribute deve ser um array.',
    'before'               => 'O :attribute deve ser uma data anterior a :date.',
    'before_or_equal'      => 'O :attribute deve ser uma data igual ou anterior a :date.',
    'between'              => [
        'numeric' => 'O :attribute deve estar entre :min e :max.',
        'file'    => 'O :attribute deve estar entre :min e :max kilobytes.',
        'string'  => 'O :attribute deve estar entre :min e :max caracteres.',
        'array'   => 'O :attribute deve estar entre :min e :max itens.',
    ],
    'boolean'              => 'O :attribute deve ser verdadeiro ou falso.',
    'confirmed'            => 'O :attribute de confirmação não corresponde.',
    'date'                 => 'O :attribute não é uma data valida.',
    'date_format'          => 'O :attribute não corresponde ao formato :format.',
    'different'            => 'O :attribute e :other devem ser diferentes.',
    'digits'               => 'O :attribute deve conter :digits digitos.',
    'digits_between'       => 'O :attribute deve conter entre :min e :max digitos.',
    'dimensions'           => 'O :attribute tem dimensões inválidas.',
    'distinct'             => 'O :attribute contem valores duplicados.',
    'email'                => 'O :attribute deve ser um e-mail válido.',
    'exists'               => 'O :attribute selecionado é inválido.',
    'file'                 => 'O :attribute deve ser um arquivo.',
    'filled'               => 'O :attribute deve ser preenchido.',
    'gt'                   => [
        'numeric' => 'O :attribute deve ser maior que :value.',
        'file'    => 'O :attribute deve ser maior que :value kilobytes.',
        'string'  => 'O :attribute deve conter mais que :value caracteres.',
        'array'   => 'O :attribute deve conter mais que :value itens.',
    ],
    'gte'                  => [
        'numeric' => 'O :attribute deve ser maior ou igual :value.',
        'file'    => 'O :attribute deve ser maior ou igual :value kilobytes.',
        'string'  => 'O :attribute deve ter no mínimo :value caracteres.',
        'array'   => 'O :attribute deve conter :value itens ou mais.',
    ],
    'image'                => 'O :attribute deve ser uma imagem.',
    'in'                   => 'O :attribute selecionado é inválido.',
    'in_array'             => 'O :attribute campo não existe em :other.',
    'integer'              => 'O :attribute deve ser um número inteiro.',
    'ip'                   => 'O :attribute deve conter um IP válido.',
    'ipv4'                 => 'O :attribute deve conter um IPv4 válido.',
    'ipv6'                 => 'O :attribute deve conter um IPv6 válido.',
    'json'                 => 'O :attribute deve conter um JSON válido.',
    'lt'                   => [
        'numeric' => 'O :attribute deve ser menor que :value.',
        'file'    => 'O :attribute deve ser menor que :value kilobytes.',
        'string'  => 'O :attribute deve conter menos que :value caracteres.',
        'array'   => 'O :attribute deve conter menos que :value itens.',
    ],
    'lte'                  => [
        'numeric' => 'O :attribute deve ser menor ou igual a :value.',
        'file'    => 'O :attribute deve ser menor ou igual a :value kilobytes.',
        'string'  => 'O :attribute deve conter no máximo :value caracteres.',
        'array'   => 'O :attribute não deve ter mais que :value itens.',
    ],
    'max'                  => [
        'numeric' => 'O :attribute não pode ser maior que :max.',
        'file'    => 'O :attribute não pode ser maior que :max kilobytes.',
        'string'  => 'O :attribute não pode conter mais que :max caracteres.',
        'array'   => 'O :attribute pode não conter mais do que :max itens.',
    ],
    'mimes'                => 'O :attribute deve ser um arquivo tipo: :values.',
    'mimetypes'            => 'O :attribute deve ser um arquivo tipo: :values.',
    'min'                  => [
        'numeric' => 'O :attribute mínimo de :min.',
        'file'    => 'O :attribute deve conter no mínimo :min kilobytes.',
        'string'  => 'O :attribute deve conter no mínimo :min caracteres.',
        'array'   => 'O :attribute deve conter pelo menos :min itens.',
    ],
    'not_in'               => 'O :attribute selecionado é inválido.',
    'not_regex'            => 'O :attribute é um formato inválido.',
    'numeric'              => 'O :attribute deve ser um número.',
    'present'              => 'O :attribute deve estar presente.',
    'regex'                => 'O :attribute formato inválido.',
    'required'             => 'O :attribute é obrigatório.',
    'required_if'          => 'O :attribute é obrigatório quando :other é :value.',
    'required_unless'      => 'O :attribute  é obrigatório a menos que :other esta em :values.',
    'required_with'        => 'O :attribute é obrigatório quando :values está presente.',
    'required_with_all'    => 'O :attribute é obrigatório quando :values está presente.',
    'required_without'     => 'O :attribute é obrigatório quando :values não está presente.',
    'required_without_all' => 'O :attribute é obrigatório quando nenhum do :values estão presentes.',
    'same'                 => 'O :attribute e :other devem ser iguais.',
    'size'                 => [
        'numeric' => 'O :attribute deve ter :size.',
        'file'    => 'O :attribute deve conter :size kilobytes.',
        'string'  => 'O :attribute deve conter :size caracteres.',
        'array'   => 'O :attribute deve conter :size itens.',
    ],
    'string'               => 'O :attribute deve conter somente caracteres.',
    'timezone'             => 'O :attribute deve ser um horário válido.',
    'unique'               => 'O :attribute já existe.',
    'uploaded'             => 'O :attribute não conseguiu fazer upload.',
    'url'                  => 'O :attribute é inválido.',

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
