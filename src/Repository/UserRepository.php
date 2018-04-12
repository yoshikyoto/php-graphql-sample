<?php

namespace UtakataQL\Repository;

/**
 * よくあるRepository
 * これはダミーですが実際にはDBに接続したりAPIを叩いたりする
 * 本物のRepositoryを実装します。
 */
class UserRepository
{

    public function getUser($id)
    {
        $dummyData = [
            1 => new User(1, 'Sakamoto'),
            2 => new User(2, 'Sato'),
            3 => new User(3, 'Tanaka'),
        ];
        return $dummyData[$id];
    }
}

/**
 * よくあるエンティティ
 */
class User
{
    private $id;
    private $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }
}