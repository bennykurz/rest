<?php
use N86io\Rest\Tests\DomainObject\FakeEntity1;
use N86io\Rest\Tests\DomainObject\FakeEntity2;
use N86io\Rest\Tests\DomainObject\FakeEntity4;

return [
    FakeEntity1::class => [
        'table' => 'table_fake_2',
        'properties' => [
            'string' => ['ordering' => false, 'hide' => true]
        ]
    ],
    FakeEntity2::class => [
        'mode' => ['write']
    ],
    FakeEntity4::class => [
        'mode' => ['read']
    ]
];
