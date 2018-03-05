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

    'accepted'             => 'Πρέπει να αποδεχτείτε το :attribute.',
    'active_url'           => 'Το :attribute δεν είναι έγκυρο URL.',
    'after'                => 'Το :attribute πρέπει να είναι ημερομηνία μεταγενέστερη της :date.',
    'after_or_equal'       => 'Το :attribute πρέπει να είναι ημερομηνία ίση ή μεταγενέστερη της :date.',
    'alpha'                => 'Το :attribute μπορεί να περιέχει μόνον γράμματα.',
    'alpha_dash'           => 'Το :attribute μπορεί να περιέχει μόνον γράμματα, αριθμούς, και παύλες.',
    'alpha_num'            => 'Το :attribute μπορεί να περιέχει μόνον γράμματα και αριθμούς.',
    'array'                => 'Το :attribute πρέπει να είναι πίνακας.',
    'before'               => 'Το :attribute πρέπει να είναι ημερομηνία προγενέστερη της :date.',
    'before_or_equal'      => 'Το :attribute πρέπει να είναι ημερομηνία ίση ή προγενέστερη της :date.',
    'between'              => [
        'numeric' => 'Το :attribute πρέπει να είναι μεταξύ :min και :max.',
        'file'    => 'Το :attribute πρέπει να είναι μεταξύ :min και :max kilobyte.',
        'string'  => 'Το :attribute πρέπει να είναι μεταξύ :min και :max χαρακτήρες.',
        'array'   => 'Το :attribute πρέπει να είναι μεταξύ :min και :max στοιχεία.',
    ],
    'boolean'              => 'Το πεδίο :attribute πρέπει να είναι σωστό ή λάθος.',
    'confirmed'            => 'Η επιβεβαίωση :attribute δεν ταιριάζει.',
    'date'                 => 'Το :attribute δεν είναι έγκυρη ημερομηνία.',
    'date_format'          => 'Το :attribute δεν ταιριάζει στην μορφή :format.',
    'different'            => 'Το :attribute και :other πρέπει να είναι διαφορετικά.',
    'digits'               => 'Το :attribute πρέπει να είναι :digits ψηφία.',
    'digits_between'       => 'Το :attribute πρέπει να είναι μεταξύ :min και :max ψηφίων.',
    'dimensions'           => 'Το :attribute δεν έχει έγκυρες διαστάσεις εικόνας.',
    'distinct'             => 'Το :attribute έχει διπλότυπη τιμή.',
    'email'                => 'Το :attribute πρέπει να είναι έγκυρη διεύθυνση email.',
    'exists'               => 'Το επιλεγμένο :attribute δεν είναι έγκυρο.',
    'file'                 => 'Το :attribute πρέπει να είναι αρχείο.',
    'filled'               => 'Το :attribute πρέπει να έχει μια τιμή.',
    'image'                => 'Το :attribute πρέπει να είναι εικόνα.',
    'in'                   => 'Το επιλεγμένο :attribute δεν είναι έγκυρο.',
    'in_array'             => 'Το πεδίο :attribute δεν υπάρχει στο :oΤοr.',
    'integer'              => 'Το :attribute πρέπει να είναι ακέραιος.',
    'ip'                   => 'Το :attribute πρέπει να είναι έγκυρη διέυθυνση IP.',
    'ipv4'                 => 'Το :attribute πρέπει να είναι έγκυρη διέυθυνση IPv4.',
    'ipv6'                 => 'Το :attribute πρέπει να είναι έγκυρη διέυθυνση IPv6.',
    'json'                 => 'Το :attribute πρέπει να είναι έγκυρο JSON.',
    'max'                  => [
        'numeric' => 'Το :attribute δεν μπορεί να είναι μεγαλύτερο από :max.',
        'file'    => 'Το :attribute δεν μπορεί να είναι μεγαλύτερο από :max kilobyte.',
        'string'  => 'Το :attribute δεν μπορεί να είναι μεγαλύτερο από :max χαρακτήρες.',
        'array'   => 'Το :attribute δεν μπορεί να έχει περισσότερα από :max στοιχεία.',
    ],
    'mimes'                => 'Το :attribute πρέπει να είναι αρχείο τύπου: :values.',
    'mimetypes'            => 'Τα :attribute πρέπει να είναι αρχεία τύπου: :values.',
    'min'                  => [
        'numeric' => 'Το :attribute πρέπει να είναι τουλάχιστον :min.',
        'file'    => 'Το :attribute πρέπει να είναι τουλάχιστον :min kilobyte.',
        'string'  => 'Το :attribute πρέπει να είναι τουλάχιστον :min χαρακτήρες.',
        'array'   => 'Το :attribute πρέπει να έχει τουλάχιστον :min στοιχεία.',
    ],
    'not_in'               => 'Το επιλεγμένο :attribute δεν είναι έγκυρο.',
    'numeric'              => 'Το :attribute πρέπει να είναι αριθμός.',
    'present'              => 'Το πεδίο :attribute δεν υπάρχει.',
    'regex'                => 'Η μορφή του :attribute δεν είναι έγκυρη.',
    'required'             => 'Το πεδίο :attribute απαιτείται.',
    'required_if'          => 'Το πεδίο :attribute απαιτείται όταν το :oΤοr είναι :value.',
    'required_unless'      => 'Το πεδίο :attribute απαιτείται εκτός αν το  :oΤοr είναι :values.',
    'required_with'        => 'Το πεδίο :attribute απαιτείται όταν υπάρχει ένα εκ των :values.',
    'required_with_all'    => 'Το πεδίο :attribute απαιτείται όταν υπάρχουν τα :values.',
    'required_without'     => 'Το πεδίο :attribute απαιτείται όταν δεν υπάρχει κάποιο εκ των :values.',
    'required_without_all' => 'Το πεδίο :attribute απαιτείται όταν δεν υπάρχουν τα :values.',
    'same'                 => 'Το :attribute και το :oΤοr πρέπει να ταιριάζουν.',
    'size'                 => [
        'numeric' => 'Το :attribute πρέπει να είναι :size.',
        'file'    => 'Το :attribute πρέπει να είναι :size kilobyte.',
        'string'  => 'Το :attribute πρέπει να είναι :size χαρακτήρες.',
        'array'   => 'Το :attribute πρέπει να περιέχει :size στοιχεία.',
    ],
    'string'               => 'Το :attribute πρέπει να είναι συμβολοσειρά.',
    'timezone'             => 'Το :attribute πρέπει να είναι έγκυρη ζώνη ώρας.',
    'unique'               => 'Το :attribute υπάρχει ήδη.',
    'uploaded'             => 'Το :attribute δεν μπόρεσε να ανέβει.',
    'url'                  => 'Η μορφή του :attribute δεν είναι έγκυρη.',

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
