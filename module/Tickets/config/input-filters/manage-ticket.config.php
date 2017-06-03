<?php

return [
    'delegate' => [
        'type' => \Zend\InputFilter\InputFilter::class,
        'firstname' => [
            'required' => false,
        ],
        'lastname' => [
            'required' => false,
        ],
        'email' => [
            'required' => false,
        ],
        'company' => [
            'required' => false,
        ],
        'twitter' => [
            'required' => false,
        ],
        'requirements' => [
            'required' => false,
        ],
    ],
];
