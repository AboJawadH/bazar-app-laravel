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
    'accepted' => 'يجب قبول :attribute.',
    'accepted_if' => ':attribute يجب قبوله عندما :other تكون :value.',
    'active_url' => ':attribute لا يُمثّل رابطًا صحيحًا.',
    'after' => 'يجب على :attribute أن يكون تاريخًا لاحقًا للتاريخ :date.',
    'after_or_equal' => ':attribute يجب أن يكون تاريخاً لاحقاً أو مطابقاً للتاريخ :date.',
    'alpha' => 'يجب أن لا يحتوي :attribute سوى على حروف.',
    'alpha_dash' => 'يجب أن لا يحتوي :attribute سوى على حروف، أرقام ومطّات.',
    'alpha_num' => 'يجب أن يحتوي :attribute على حروفٍ وأرقامٍ فقط.',
    'ascii' => 'يجب أن يحتوي حقل :attribute فقط على أحرف ورموز أبجدية رقمية أحادية البايت.',
    'array' => 'يجب أن يكون :attribute ًمصفوفة.',
    'before' => 'يجب على :attribute أن يكون تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal' => ':attribute يجب أن يكون تاريخا سابقا أو مطابقا للتاريخ :date.',
    'between' => [
        'array' => 'يجب أن يحتوي :attribute على عدد من العناصر بين :min و :max.',
        'file' => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'string' => 'يجب أن يكون عدد حروف النّص :attribute بين :min و :max.',
    ],
    'boolean' => 'يجب أن تكون قيمة :attribute إما true أو false .',
    'confirmed' => 'حقل التأكيد غير مُطابق للحقل :attribute.',
    'current_password' => 'كلمة السر غير صحيحة.',
    'date' => ':attribute ليس تاريخًا صحيحًا.',
    'date_equals' => 'يجب أن يكون :attribute مطابقاً للتاريخ :date.',
    'date_format' => 'لا يتوافق :attribute مع الشكل :format.',
    'decimal' => 'يجب أن يحتوي حقل :attribute على منازل عشرية :decimal.',
    'declined' => ':attribute يجب رفضها.',
    'declined_if' => ':attribute يجب رفضها عندما :other تكون :value.',
    'different' => 'يجب أن يكون الحقلان :attribute و :other مُختلفين.',
    'digits' => 'يجب أن يحتوي :attribute على :digits رقمًا/أرقام.',
    'digits_between' => 'يجب أن يحتوي :attribute بين :min و :max رقمًا/أرقام .',
    'dimensions' => ':attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'للحقل :attribute قيمة مُكرّرة.',
    'doesnt_end_with' => ':attribute يجب أن لا تنتهي بأحد الأمور التالية: :values.',
    'doesnt_start_with' => ':attribute يحب أن لا تبدأ بأحد الأمور التالية: :values.',
    'email' => 'يجب أن يكون :attribute عنوان بريد إلكتروني صحيح البُنية.',
    'ends_with' => ':attribute يجب أن تنتهي بأحد الأمور التالية: :values.',
    'enum' => 'القيمة المختارة :attribute غير صحيحة.',
    'exists' => 'القيمة المحددة :attribute غير موجودة.',
    'file' => ':attribute يجب أن يكون ملفا.',
    'filled' => ':attribute إجباري.',
    'gt' => [
        'array' => 'يجب أن يحتوي :attribute على أكثر من :value عناصر/عنصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من :value.',
        'string' => 'يجب أن يكون طول النّص :attribute أكثر من :value حروفٍ/حرفًا.',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي :attribute على الأقل على :value عُنصرًا/عناصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر من :value.',
        'string' => 'يجب أن يكون طول النص :attribute على الأقل :value حروفٍ/حرفًا.',
    ],
    'image' => 'يجب أن يكون :attribute صورةً.',
    'in' => ':attribute غير موجود.',
    'in_array' => ':attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون :attribute عددًا صحيحًا.',
    'ip' => 'يجب أن يكون :attribute عنوان IP صحيحًا.',
    'ipv4' => 'يجب أن يكون :attribute عنوان IPv4 صحيحًا.',
    'ipv6' => 'يجب أن يكون :attribute عنوان IPv6 صحيحًا.',
    'json' => 'يجب أن يكون :attribute نصآ من نوع JSON.',
    'lowercase' => 'يجب أن يكون حقل :attribute صغيرًا.',
    'lt' => [
        'array' => 'يجب أن يحتوي :attribute على أقل من :value عناصر/عنصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute أصغر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute أصغر من :value.',
        'string' => 'يجب أن يكون طول النّص :attribute أقل من :value حروفٍ/حرفًا.',
    ],
    'lte' => [
        'array' => 'يجب أن لا يحتوي :attribute على أكثر من :value عناصر/عنصر.',
        'file' => 'يجب أن لا يتجاوز حجم الملف :attribute :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر من :value.',
        'string' => 'يجب أن لا يتجاوز طول النّص :attribute :value حروفٍ/حرفًا.',
    ],
    'mac_address' => ':attribute يجب أن يكون عنوان ماك صحيح.',
    'max' => [
        'array' => 'يجب أن لا يحتوي :attribute على أكثر من :max عناصر/عنصر.',
        'file' => 'يجب أن لا يتجاوز حجم الملف :attribute :max كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أصغر من :max.',
        'string' => 'يجب أن لا يتجاوز طول النّص :attribute :max حروفٍ/حرفًا.',
    ],
    'max_digits' => ':attribute يجب أن لا تحتوي أكثر من :max رقم.',
    'mimes' => 'يجب أن يكون ملفًا من نوع : :values.',
    'mimetypes' => 'يجب أن يكون ملفًا من نوع : :values.',
    'min' => [
        'array' => 'يجب أن يحتوي :attribute على الأقل على :min عُنصرًا/عناصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية أو أكبر من :min.',
        'string' => 'يجب أن يكون طول النص :attribute على الأقل :min حروفٍ/حرفًا.',
    ],
    'min_digits' => ':attribute يجب أن تحتوي على الأقل :min رقم.',
    'missing' => 'يجب أن يكون حقل :attribute مفقودًا.',
    'missing_if' => 'يجب أن يكون الحقل :attribute مفقودًا عندما يكون :other هو :value.',
    'missing_unless' => 'يجب أن يكون الحقل :attribute مفقودًا ما لم يكن :other هو :value.',
    'missing_with' => 'يجب أن يكون حقل :attribute مفقودًا عند وجود :values.',
    'missing_with_all' => 'يجب أن يكون حقل السمة : مفقودًا عند وجود القيم :.',
    'multiple_of' => ':attribute يجب أن يكون من مضاعفات :value.',
    'not_in' => ':attribute موجود.',
    'not_regex' => 'صيغة :attribute غير صحيحة.',
    'numeric' => 'يجب على :attribute أن يكون رقمًا.',
    'password' => [
        'letters' => ':attribute يجب أن تحوي على الأقل حرفاً واحداً.',
        'mixed' => ':attribute يجب أن تحوي على الأقل حرف كبير و حرف صغير.',
        'numbers' => ':attribute يجب أن تحتوي على رقم.',
        'symbols' => ':attribute يجب أن تحتوي على إشارة.',
        'uncompromised' => ':attribute المدخلة غير قوية.',
    ],
    'present' => 'يجب تقديم :attribute.',
    'prohibited' => ':attribute محظور.',
    'prohibited_if' => ':attribute محظور عندما :other تكون :value.',
    'prohibited_unless' => ':attribute محظور إلا إذا :other كان من ضمن :values.',
    'prohibits' => ':attribute يحظر :other من كونه موجوداً.',
    'regex' => 'صيغة :attribute .غير صحيحة.',
    'required' => ':attribute مطلوب.',
    'required_array_keys' => ':attribute يجب أن تحتوي على: :values.',
    'required_if' => ':attribute مطلوب في حال ما إذا كان :other يساوي :value.',
    'required_if_accepted' => 'الحقل :attribute مطلوب عند قبول :other.',
    'required_unless' => ':attribute مطلوب في حال ما لم يكن :other يساوي :values.',
    'required_with' => ':attribute مطلوب إذا توفّر :values.',
    'required_with_all' => ':attribute مطلوب إذا توفّر :values.',
    'required_without' => ':attribute مطلوب إذا لم يتوفّر :values.',
    'required_without_all' => ':attribute مطلوب إذا لم يتوفّر :values.',
    'same' => 'يجب أن يتطابق :attribute مع :other.',
    'size' => [
        'array' => 'يجب أن يحتوي :attribute على :size عنصرٍ/عناصر بالضبط.',
        'file' => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute مساوية لـ :size.',
        'string' => 'يجب أن يحتوي النص :attribute على :size حروفٍ/حرفًا بالضبط.',
    ],
    'starts_with' => 'يجب أن يبدأ :attribute بأحد القيم التالية: :values',
    'string' => 'يجب أن يكون :attribute نصًا.',
    'timezone' => 'يجب أن يكون :attribute نطاقًا زمنيًا صحيحًا.',
    'unique' => 'قيمة :attribute مُستخدمة من قبل.',
    'uploaded' => 'فشل في تحميل :attribute.',
    'uppercase' => 'يجب أن يكون حقل :attribute كبيرًا.',
    'url' => 'صيغة الرابط :attribute غير صحيحة.',
    'ulid' => 'يجب أن يكون حقل :attribute ULID صالحًا.',
    'uuid' => ':attribute يجب أن يكون بصيغة UUID سليمة.',

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
