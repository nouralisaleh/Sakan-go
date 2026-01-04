<?php

return [

    /*
    |--------------------------------------------------------------------------
    | رسائل التحقق الافتراضية
    |--------------------------------------------------------------------------
    |
    | هذه الرسائل تستخدمها Validator class في Laravel. يمكن تعديلها كما تشاء.
    |
    */

    'accepted' => 'يجب قبول حقل :attribute.',
    'accepted_if' => 'يجب قبول حقل :attribute عندما يكون :other يساوي :value.',
    'active_url' => 'حقل :attribute ليس رابطًا صالحًا.',
    'after' => 'يجب أن يكون حقل :attribute بعد :date.',
    'after_or_equal' => 'يجب أن يكون حقل :attribute بعد أو يساوي :date.',
    'alpha' => 'حقل :attribute يجب أن يحتوي على حروف فقط.',
    'alpha_dash' => 'حقل :attribute يجب أن يحتوي على حروف وأرقام وشرطات وشرطات سفلية فقط.',
    'alpha_num' => 'حقل :attribute يجب أن يحتوي على حروف وأرقام فقط.',
    'array' => 'حقل :attribute يجب أن يكون مصفوفة.',
    'before' => 'يجب أن يكون حقل :attribute قبل :date.',
    'before_or_equal' => 'يجب أن يكون حقل :attribute قبل أو يساوي :date.',
    'between' => [
        'array' => 'حقل :attribute يجب أن يحتوي بين :min و :max عناصر.',
        'file' => 'حقل :attribute يجب أن يكون بين :min و :max كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون بين :min و :max.',
        'string' => 'حقل :attribute يجب أن يكون بين :min و :max حروف.',
    ],
    'boolean' => 'حقل :attribute يجب أن يكون صحيح أو خطأ.',
    'confirmed' => 'تأكيد حقل :attribute غير مطابق.',
    'date' => 'حقل :attribute ليس تاريخًا صالحًا.',
    'date_equals' => 'حقل :attribute يجب أن يساوي التاريخ :date.',
    'date_format' => 'حقل :attribute لا يتطابق مع التنسيق :format.',
    'different' => 'حقل :attribute و :other يجب أن يكونا مختلفين.',
    'digits' => 'حقل :attribute يجب أن يكون :digits أرقام.',
    'digits_between' => 'حقل :attribute يجب أن يكون بين :min و :max أرقام.',
    'dimensions' => 'أبعاد الصورة في حقل :attribute غير صالحة.',
    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',
    'email' => 'حقل :attribute يجب أن يكون بريدًا إلكترونيًا صالحًا.',
    'ends_with' => 'حقل :attribute يجب أن ينتهي بأحد القيم التالية: :values.',
    'exists' => 'القيمة المختارة في :attribute غير صالحة.',
    'file' => 'حقل :attribute يجب أن يكون ملفًا.',
    'filled' => 'حقل :attribute يجب أن يحتوي على قيمة.',
    'gt' => [
        'array' => 'حقل :attribute يجب أن يحتوي على أكثر من :value عنصر.',
        'file' => 'حقل :attribute يجب أن يكون أكبر من :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أكبر من :value.',
        'string' => 'حقل :attribute يجب أن يكون أكبر من :value حروف.',
    ],
    'gte' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :value عناصر أو أكثر.',
        'file' => 'حقل :attribute يجب أن يكون أكبر أو يساوي :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أكبر أو يساوي :value.',
        'string' => 'حقل :attribute يجب أن يكون أكبر أو يساوي :value حروف.',
    ],
    'image' => 'حقل :attribute يجب أن يكون صورة.',
    'in' => 'القيمة المختارة في :attribute غير صالحة.',
    'in_array' => 'حقل :attribute غير موجود ضمن :other.',
    'integer' => 'حقل :attribute يجب أن يكون رقمًا صحيحًا.',
    'ip' => 'حقل :attribute يجب أن يكون عنوان IP صالحًا.',
    'json' => 'حقل :attribute يجب أن يكون نص JSON صالحًا.',
    'max' => [
        'array' => 'حقل :attribute لا يجب أن يحتوي على أكثر من :max عناصر.',
        'file' => 'حقل :attribute لا يجب أن يكون أكبر من :max كيلوبايت.',
        'numeric' => 'حقل :attribute لا يجب أن يكون أكبر من :max.',
        'string' => 'حقل :attribute لا يجب أن يكون أكبر من :max حروف.',
    ],
    'min' => [
        'array' => 'حقل :attribute يجب أن يحتوي على الأقل على :min عناصر.',
        'file' => 'حقل :attribute يجب أن يكون على الأقل :min كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون على الأقل :min.',
        'string' => 'حقل :attribute يجب أن يكون على الأقل :min حروف.',
    ],
    'numeric' => 'حقل :attribute يجب أن يكون رقمًا.',
    'present' => 'حقل :attribute يجب أن يكون موجودًا.',
    'regex' => 'صيغة حقل :attribute غير صالحة.',
    'required' => 'حقل :attribute مطلوب.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other يساوي :value.',
    'required_unless' => 'حقل :attribute مطلوب ما لم يكن :other ضمن :values.',
    'required_with' => 'حقل :attribute مطلوب عندما يكون :values موجودًا.',
    'required_with_all' => 'حقل :attribute مطلوب عندما تكون :values موجودة.',
    'required_without' => 'حقل :attribute مطلوب عندما لا يكون :values موجودًا.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا تكون أي من :values موجودة.',
    'same' => 'حقل :attribute يجب أن يطابق :other.',
    'size' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :size عناصر.',
        'file' => 'حقل :attribute يجب أن يكون :size كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون :size.',
        'string' => 'حقل :attribute يجب أن يكون :size حروف.',
    ],
    'starts_with' => 'حقل :attribute يجب أن يبدأ بأحد القيم التالية: :values.',
    'string' => 'حقل :attribute يجب أن يكون نصًا.',
    'timezone' => 'حقل :attribute يجب أن يكون منطقة زمنية صحيحة.',
    'unique' => 'قيمة :attribute موجودة بالفعل.',
    'url' => 'حقل :attribute يجب أن يكون رابطًا صالحًا.',

    /*
    |--------------------------------------------------------------------------
    | رسائل مخصصة
    |--------------------------------------------------------------------------
    */
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'رسالة مخصصة',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | أسماء الحقول
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'phone' => 'الهاتف',
        'country_code' => 'رمز الدولة',
        'otp' => 'رمز التحقق',
    ],

];
