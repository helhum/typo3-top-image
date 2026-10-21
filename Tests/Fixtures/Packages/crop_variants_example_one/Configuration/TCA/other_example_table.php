<?php

return [
    'ctrl' => [
        'label' => 'other_example_field',
    ],
    'columns' => [
        'other_example_field' => [
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
            ],
        ],
    ],
    'types' => [
        'one' => [
            'showitem' => 'other_example_field'
        ],
        'two' => [
            'showitem' => 'other_example_field'
        ],
    ],
];
