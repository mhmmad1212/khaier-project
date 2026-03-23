<?php

return [
    'login' => [
        'heading' => 'تسجيل الدخول',
        'actions' => [
            'authenticate' => [
                'label' => 'دخول',
            ],
        ],
        'form' => [
            'email' => [
                'label' => 'البريد الإلكتروني',
            ],
            'password' => [
                'label' => 'كلمة المرور',
            ],
            'remember' => [
                'label' => 'تذكرني',
            ],
        ],
    ],
    'password-reset' => [
        'request' => [
            'heading' => 'نسيت كلمة المرور',
            'actions' => [
                'request' => [
                    'label' => 'إرسال رابط إعادة التعيين',
                ],
            ],
            'form' => [
                'email' => [
                    'label' => 'البريد الإلكتروني',
                ],
            ],
        ],
        'reset' => [
            'heading' => 'إعادة تعيين كلمة المرور',
            'actions' => [
                'reset' => [
                    'label' => 'إعادة تعيين كلمة المرور',
                ],
            ],
            'form' => [
                'email' => [
                    'label' => 'البريد الإلكتروني',
                ],
                'password' => [
                    'label' => 'كلمة المرور الجديدة',
                ],
                'password_confirmation' => [
                    'label' => 'تأكيد كلمة المرور',
                ],
            ],
        ],
    ],
];
