<?php

namespace Application;

use Zend\Form\ElementFactory;

return [
    'form_elements' => [
        'aliases' => [
            'itemfullname' => Form\Element\ItemFullName::class,
            'itemFullName' => Form\Element\ItemFullName::class,
            'ItemFullName' => Form\Element\ItemFullName::class,
            'itembody' => Form\Element\ItemBody::class,
            'itemBody' => Form\Element\ItemBody::class,
            'ItemBody' => Form\Element\ItemBody::class,
            'itemname' => Form\Element\ItemName::class,
            'itemName' => Form\Element\ItemName::class,
            'ItemName' => Form\Element\ItemName::class,
            'year' => Form\Element\Year::class,
            'Year' => Form\Element\Year::class,
            'userpassword' => Form\Element\UserPassword::class,
            'userPassword' => Form\Element\UserPassword::class,
            'UserPassword' => Form\Element\UserPassword::class,
        ],
        'factories' => [
            Form\Element\ItemFullName::class => ElementFactory::class,
            Form\Element\ItemBody::class     => ElementFactory::class,
            Form\Element\ItemName::class     => ElementFactory::class,
            Form\Element\Year::class         => ElementFactory::class,
            Form\Element\UserPassword::class => ElementFactory::class,
        ]
    ],
    'forms' => [
        'DeleteUserForm' => [
            'type'     => 'Zend\Form\Form',
            'attributes'  => [
                'method' => 'post',
            ],
            'elements' => [
                [
                    'spec' => [
                        'type' => Form\Element\UserPassword::class,
                        'name' => 'password'
                    ],
                ]
            ],
            'input_filter' => [
                'password' => [
                    'required' => true
                ]
            ],
        ],
        'AttrsLogFilterForm' => [
            'type'     => 'Zend\Form\Form',
            'attributes'  => [
                'method' => 'post'
            ],
            'elements' => [
                [
                    'spec' => [
                        'type'    => 'Text',
                        'name'    => 'user_id',
                        'options' => [
                            'label' => 'specifications-editor/log/filter/user-id'
                        ]
                    ]
                ]
            ]
        ],
    ]
];
