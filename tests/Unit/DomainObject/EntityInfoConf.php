<?php
return [
    \N86io\Rest\Tests\DomainObject\FakeEntity4::class => [
        'table' => 'table_fake',
        'mode' => ['read', 'write'],
        'properties' => [
            'string' => [
                'ordering' => true
            ]
        ]
    ]
];
