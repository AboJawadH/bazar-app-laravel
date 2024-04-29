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

    'accepted' => ':attribute kabul edilmelidir.',
    'accepted_if' => 'The :attribute field must be accepted when :other is :value.',
    'active_url' => ':attribute geçerli bir URL olmalıdır.',
    'after' => ':attribute şundan daha eski bir tarih olmalıdır :date.',
    'after_or_equal' => 'The :attribute field must be a date after or equal to :date.',
    'alpha' => ':attribute sadece harflerden oluşmalıdır.',
    'alpha_dash' => ':attribute sadece harfler, rakamlar ve tirelerden oluşmalıdır.',
    'alpha_num' => ':attribute sadece harfler ve rakamlar içermelidir.',
    'array' => ':attribute dizi olmalıdır.',
    'ascii' => 'The :attribute field must only contain single-byte alphanumeric characters and symbols.',
    'before' => ':attribute şundan daha önceki bir tarih olmalıdır :date.',
    'before_or_equal' => ':attribute şu tarihe eşit yada önceki bir tarih olmalıdır :date.',
    'between' => [
        'array' => ':attribute :min - :max arasında nesneye sahip olmalıdır.',
        'file' => ':attribute :min - :max arasındaki kilobayt değeri olmalıdır.',
        'numeric' => ':attribute :min - :max arasında olmalıdır.',
        'string' => ':attribute :min - :max arasında karakterden oluşmalıdır.',
    ],
    'boolean' => ':attribute TRUE ya da FALSE içeren bir değer olmalıdır',
    'confirmed' => ':attribute tekrarı eşleşmiyor.',
    'current_password' => 'The password is incorrect.',
    'date' => ':attribute geçerli bir tarih olmalıdır.',
    'date_equals' => 'The :attribute field must be a date equal to :date.',
    'date_format' => ':attribute :format biçimi ile eşleşmiyor.',
    'decimal' => 'The :attribute field must have :decimal decimal places.',
    'declined' => 'The :attribute field must be declined.',
    'declined_if' => 'The :attribute field must be declined when :other is :value.',
    'different' => ':attribute ile :other birbirinden farklı olmalıdır.',
    'digits' => ':attribute :digits rakam olmalıdır.',
    'digits_between' => ':attribute :min ile :max arasında rakam olmalıdır.',
    'dimensions' => ':attribute resim boyutları geçersiz.',
    'distinct' => ':attribute değeri yineleniyor.',
    'doesnt_end_with' => 'The :attribute field must not end with one of the following: :values.',
    'doesnt_start_with' => 'The :attribute field must not start with one of the following: :values.',
    'email' => ':attribute biçimi geçersiz.',
    'ends_with' => ':attribute şunlardan biri ile bitmelidir :values',
    'enum' => 'The selected :attribute is invalid.',
    'exists' => 'Seçili :attribute geçersiz.',
    'file' => ':attribute bir dosya olmalıdır',
    'filled' => ':attribute bir değere sahip olmalı.',
    'gt' => [
        'array' => ':attribute :value öğesinden fazlasına sahip olmalıdır.',
        'file' => ':attribute :value KB den büyük olmalıdır.',
        'numeric' => ':attribute :value dan büyük olmalıdır.',
        'string' => ':attribute :value karakterlerinden büyük olmalıdır.',
    ],
    'gte' => [
        'array' => ':attribute :value öğelerine veya daha fazlasına sahip olmalıdır.',
        'file' => ':attribute :value KB den büyük veya eşit olmalıdır.',
        'numeric' => ':attribute :value büyük veya eşit olmalıdır.',
        'string' => ':attribute :value karakterlerinden büyük veya eşit olmalıdır.',
    ],
    'image' => ':attribute alanı resim dosyası olmalıdır.',
    'in' => ':attribute değeri geçersiz.',
    'in_array' => ':attribute şunların :other. içinde mevcut değil.',
    'integer' => ':attribute rakam olmalıdır.',
    'ip' => ':attribute geçerli bir IP adresi olmalıdır.',
    'ipv4' => ':attribute geçerli bir IPv4 adresi olmalı.',
    'ipv6' => ':attribute geçerli bir IPv6 adresi olmalı.',
    'json' => ':attribute geçerli bir JSON dizisi olmalı.',
    'lowercase' => 'The :attribute field must be lowercase.',
    'lt' => [
        'array' => ':attribute şunlardan  :value  fazla olmamalı',
        'file' => ':attribute şunlardan kilobytes olarak :value küçük olmalıdır.',
        'numeric' => ':attribute şunlardan :value. küçük olmalıdır.',
        'string' => ':attribute şunlardan karakter olarak  :value küçük olmalıdır.',
    ],
    'lte' => [
        'array' => ':attribute şunlardan fazla olmamalı :value .',
        'file' => ':attribute şunlardan kilobytes olarak :value küçük veya eşit olmalıdır.',
        'numeric' => ':attribute şunlardan numeric olarak :value. küçük veya eşit olmalıdır.',
        'string' => ':attribute şunlardan karakter olarak  :value küçük veya eşit olmalıdır.',
    ],
    'mac_address' => 'The :attribute field must be a valid MAC address.',
    'max' => [
        'array' => ':attribute değeri :max adedinden az nesneye sahip olmalıdır.',
        'file' => ':attribute değeri :max kilobayt değerinden küçük olmalıdır.',
        'numeric' => ':attribute değeri :max değerinden küçük olmalıdır.',
        'string' => ':attribute değeri :max karakter değerinden küçük olmalıdır.',
    ],
    'max_digits' => 'The :attribute field must not have more than :max digits.',
    'mimes' => ':attribute ( :values ) Türünde bir dosya olmalıdır.',
    'mimetypes' => ':attribute bir dosya olmalı: :values.',
    'min' => [
        'array' => ':attribute en az :min nesneye sahip olmalıdır.',
        'file' => ':attribute değeri :min kilobayt değerinden büyük olmalıdır.',
        'numeric' => ':attribute değeri :min değerinden büyük olmalıdır.',
        'string' => ':attribute değeri :min karakter değerinden büyük olmalıdır.',
    ],
    'min_digits' => 'The :attribute field must have at least :min digits.',
    'missing' => 'The :attribute field must be missing.',
    'missing_if' => 'The :attribute field must be missing when :other is :value.',
    'missing_unless' => 'The :attribute field must be missing unless :other is :value.',
    'missing_with' => 'The :attribute field must be missing when :values is present.',
    'missing_with_all' => 'The :attribute field must be missing when :values are present.',
    'multiple_of' => 'The :attribute field must be a multiple of :value.',
    'not_in' => 'Seçili :attribute geçersiz.',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => ':attribute sayı olmalıdır.',
    'password' => [
        'letters' => 'The :attribute field must contain at least one letter.',
        'mixed' => 'The :attribute field must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The :attribute field must contain at least one number.',
        'symbols' => 'The :attribute field must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present' => ':attribute mevcut değil.',
    'prohibited' => 'The :attribute field is prohibited.',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.',
    'prohibits' => 'The :attribute field prohibits :other from being present.',
    'regex' => ':attribute geçersiz format.',
    'required' => ':attribute alanı gereklidir.',
    'required_array_keys' => 'The :attribute field must contain entries for: :values.',
    'required_if' => ':attribute alanı, :other :value değerine sahip olduğunda zorunludur.',
    'required_if_accepted' => 'The :attribute field is required when :other is accepted.',
    'required_unless' => ':attribute diğerlerin unless :other olmadığında zorunludur :values.',
    'required_with' => ':attribute alanı :values varken zorunludur.',
    'required_with_all' => ':attribute alanı :values varken zorunludur.',
    'required_without' => ':attribute alanı :values yokken zorunludur.',
    'required_without_all' => ':attribute alanı :values yokken zorunludur.',
    'same' => ':attribute ile :other eşleşmelidir.',
    'size' => [
        'array' => ':attribute :size nesneye sahip olmalıdır.',
        'file' => ':attribute :size kilobyte olmalıdır.',
        'numeric' => ':attribute :size olmalıdır.',
        'string' => ':attribute :size karakter olmalıdır.',
    ],
    'starts_with' => ':attribute şunlardan biri ile başlamalıdır: :values',
    'string' => ':attribute bir dize (string) değer olmalıdır.',
    'timezone' => ':attribute geçersiz bölge.',
    'unique' => ':attribute benzer kayıt mevcut.',
    'uploaded' => ':attribute yükleme başarısız.',
    'uppercase' => 'The :attribute field must be uppercase.',
    'url' => ':attribute biçimi geçersiz.',
    'ulid' => 'The :attribute field must be a valid ULID.',
    'uuid' => 'The :attribute must be a valid UUID.',
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
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
