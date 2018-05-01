<?php

namespace UtakataQL\Repository;

/**
 * よくあるRepository
 * これはダミーですが実際にはDBに接続したりAPIを叩いたりする
 * 本物のRepositoryを実装します。
 */
class UserRepository
{

    private $dummyData;

    public function __construct()
    {
        $sakamoto = new User(1, 'Sakamoto', '坂本ですが。', '京都府');
        $sato = new User(2, 'Sato', '佐藤です。', '千葉県');
        $tanaka = new User(3, 'Tanaka', '田中です。', '東京都');
        $this->dummyData = [
            1 => $sakamoto,
            2 => $sato,
            3 => $tanaka,
        ];
    }

    public function getUser($id)
    {
        return $this->dummyData[$id];
    }

}

/**
 * よくあるエンティティ
 */
class User
{
    private $id;
    private $name;
    private $profile;
    private $address;
    /** @var int[] */
    private $followerIds;

    public function __construct($id, $name, $profile, $address)
    {
        $this->id = $id;
        $this->name = $name;
        $this->profile = $profile;
        $this->address = $address;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function getAddress()
    {
        return $this->address;
    }
}
