<?php

namespace App\Utilities\Helpers;

use Redis;
use App\Utilities\Contracts\RedisHelperInterface;

class RedisHelper implements RedisHelperInterface
{

    public function __construct()
    {
        //
    }
    public function storeRecentMessage(mixed $id, string $messageSubject, string $toEmailAddress): void
    {
        Redis::rpush('emails', json_encode([
            'id' => $id,
            'subject' => $messageSubject,
            'email' => $toEmailAddress,
        ]));

        return;
    }

    public function getCachedEmails()
    {
        $cachedEmail = Redis::lrange('emails', 0, -1);

        return $cachedEmail;
    }
}
