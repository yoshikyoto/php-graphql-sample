<?php

namespace UtakataQL\Type\User;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use UtakataQL\Repository\UserRepository;
use UtakataQL\Repository\FollowRepository;
use UtakataQL\Type\Definition\DomainType;

class User extends ObjectType {

    private $userRepository;

    public function __construct() {
        // TODO 本当はちゃんとコンストラクタDIとかした方がいい
        $this->userRepository = new UserRepository;
        $this->followRepository = new FollowRepository;
        parent::__construct([
            'name' => 'User',
            'fields' => function() {
                return [
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
                    'followers' => [
                        'type' => Type::listOf(DomainType::user()),
                        'resolve' => function($userId) {
                            return $this->followRepository->getFollowerIds($userId);
                        }
                    ],
                ];
            },
        ]);
    }

    private function getUser($id) {
        return $this->userRepository->getUser($id);
    }

}
