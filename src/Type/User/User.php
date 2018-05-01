<?php

namespace UtakataQL\Type\User;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use UtakataQL\Repository\UserRepository;

class User extends ObjectType {

    private $userRepository;

    public function __construct() {
        // TODO 本当はちゃんとコンストラクタDIとかした方がいい
        $this->userRepository = new UserRepository;
        parent::__construct([
            'name' => 'User',
            'fields' => [
                'id' => [
                    'type' => Type::int(),
                    'resolve' => function ($id) {
                        return $id;
                    },
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => function($id) {
                        return $this->getUser($id)->getName();
                    }
                ],
                'profile' => [
                    'type' => Type::string(),
                    'resolve' => function($id) {
                        return $this->getUser($id)->getProfile();
                    }
                ],
                'address' => [
                    'type' => Type::string(),
                    'resolve' => function($id) {
                        return $this->getUser($id)->getAddress();
                    }
                ],
            ],
        ]);
    }

    private function getUser($id) {
        error_log(__METHOD__ . "\n", 3, "/tmp/php-graphql-sample.log");
        return $this->userRepository->getUser($id);
    }

}
