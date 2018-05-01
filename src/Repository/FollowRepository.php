<?php

namespace UtakataQL\Repository;

class FollowRepository
{
    /**
     * userIdのフォロワーを取得する
     * @param int $userId;
     * @return int[]
     */
    public function getFollowerIds($userId)
    {
        $data = [
            1 => [2],
            2 => [3],
            3 => [1],
        ];
        return $data[$userId];
    }
}
