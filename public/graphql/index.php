<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use UtakataQL\Type\Definition\DomainType;

/** Queryクラスは参照系のメソッドを持つクラス */
class Query extends ObjectType {

    public function __construct() {
        parent::__construct([
            'name' => 'Query',
            'fields' => [
                'user' => [
                    'type' => DomainType::user(),
                    'args' => [
                        'id' => Type::int(),
                    ],
                    'resolve' => function ($value, $args, $context, ResolveInfo $resolveInfo) {
                        // $argsにGraphQLの引数が入る
                        // returnした値が Type\User\User の resolve に渡る仕組み
                        return $args['id'];
                    }
                ],
            ],
        ]);
    }

}


$schema = new GraphQL\Type\Schema([
    // 参照系はquery
    'query' => new Query(),
    // 更新系はmutation
    // 'mutation' => ...
]);

$server = new GraphQL\Server\StandardServer([
    'schema' => $schema
]);

$server->handleRequest();
