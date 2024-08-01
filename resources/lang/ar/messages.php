<?php

return [
    "group" => "المحفظة",
    "withdrawal_methods" => [
        "title" => "طرق السحب",
        "columns" => [
            "logo" => "شعار",
            "name" => "الاسم",
            "minimum_amount" => "الحد الأدنى للمبلغ",
            "maximum_amount" => "الحد الأقصى للمبلغ",
            "currency" => "العملة",
            "conversion_rate" => "سعر الصرف",
            "is_active" => "مفعل",
            "description" => "الوصف",
            "active" => "مفعل",
            "inactive" => "غير مفعل",
        ],
    ],
    "withdrawal_requests" => [
        "title" => "طلبات السحب",
        "single" => "طلب سحب",
        "columns" => [
            "process" => "قيد المعالجة",
            "completed" => "مكتمل",
            "cancelled" => "ملغى",
            "currency" => "العملة",
        ],
        "notification" => [
            "completed" => [
                "title" => "تم إكمال السحب",
                "body" => "تمت معالجة طلب السحب بنجاح.",
            ],
            "insufficient" => [
                "title" => "أموال غير كافية",
                "body" => "لا تحتوي المحفظة على رصيد كافٍ لإكمال هذا السحب.",
            ],
            "cancelled" => [
                "title" => "تم إلغاء السحب",
                "body" => "تم إلغاء طلب السحب بنجاح.",
            ],
            "amount_too_low" => [
                "title" => "المبلغ منخفض جدًا",
                "body" => "المبلغ الذي أدخلته أقل من الحد الأدنى المسموح به.",
            ],
            "amount_too_high" => [
                "title" => "المبلغ مرتفع جدًا",
                "body" => "المبلغ الذي أدخلته يتجاوز الحد الأقصى المسموح به.",
            ],
            "balance_is_not_enough" => [
                "title" => "المحفظة",
                "body" => "الرصيد غير كافٍ",
            ],
            "request_in_progress" => [
                "title" => "الطلب قيد التقدم",
                "body" => "لديك طلب سحب معلق أو جارٍ معالجته. يرجى الانتظار حتى يتم اكتماله.",
            ]
        ]
    ],
    "forms" => [
        "section" => [
            "information" => "معلومات النموذج"
        ],
        "title" => "باني النماذج",
        "single" => "نموذج",
        "columns" => [
            "type" => "النوع",
            "method" => "الطريقة",
            "title" => "العنوان",
            "key" => "المفتاح",
            "description" => "الوصف",
            "endpoint" => "النقطة النهائية",
            "is_active" => "مفعل",
        ],
        "fields" => [
            "title" => "الحقول",
            "single" => "حقل",
            "columns" => [
                "type" => "النوع",
                "name" => "الاسم",
                "group" => "المجموعة",
                "default" => "القيمة الافتراضية",
                "is_relation" => "علاقة",
                "relation_name" => "اسم العلاقة",
                "relation_column" => "عمود العلاقة",
                "sub_form" => "نموذج فرعي",
                "is_multi" => "متعدد",
                "has_options" => "يمتلك خيارات",
                "options" => "الخيارات",
                "label" => "التسمية",
                "value" => "القيمة",
                "placeholder" => "نص البديل",
                "hint" => "تلميح",
                "is_required" => "مطلوب",
                "required_message" => "رسالة مطلوب",
                "has_validation" => "يمتلك تحقق",
                "validation" => "التحقق",
                "rule" => "القاعدة",
                "message" => "الرسالة"
            ],
            "tabs" => [
                "general" => "عام",
                "options" => "الخيارات",
                "validation" => "التحقق",
                "relation" => "العلاقة",
                "labels" => "التسميات",
            ],
            "actions" => [
                "preview" => "معاينة",
            ]
        ],
        "requests" => [
            "title" => "طلبات النموذج",
            "single" => "طلب",
            "columns" => [
                "status" => "الحالة",
                "description" => "الوصف",
                "time" => "الوقت",
                "date" => "التاريخ",
                "payload" => "الحمولة",
                "pending" => "قيد الانتظار",
                "processing" => "قيد المعالجة",
                "completed" => "مكتمل",
                "cancelled" => "ملغى",
                "username" => "اسم المستخدم",
                "method_name" => "اسم الطريقة",
                "amount" => "المبلغ",
                "currency" => "العملة",
                "rate" => "السعر",
                "final_amount" => "المبلغ النهائي",
            ]
        ]
    ],
];
