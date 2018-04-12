<?php

namespace UtakataQL\Type\Definition;

use UtakataQL\Type\User\User;

/**
 * GraphQL\Type\Definition\Type::string() と同様に
 * UtakataQL\Type\Definition\DomainType:user() のように型を呼べるようにする
 */
class DomainType {

    private static $user;

    public static function user() {
        // オブジェクトではなく型の定義なので シングルトンにする
        if(!isset(static::$user)) {
            static::$user = new User();
        }
        return static::$user;
    }

}
