<?php
return [
    \N86io\Rest\Examples\Example4::class => [
        'table' => 'table_fake',
        'mode' => ['read', 'write'],
        'properties' => [
            'string' => [
                'ordering' => true
            ]
        ]
    ]
];
