<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
