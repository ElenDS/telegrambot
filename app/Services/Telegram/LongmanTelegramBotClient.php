<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use Longman\TelegramBot\Telegram;

class LongmanTelegramBotClient
{
    public function createApiObject(): Telegram
    {
        $telegram = new Telegram($this->getTelegramToken(), $this->getTelegramUsername());
        $telegram->enableMySql($this->getMysqlCredentials());
        return $telegram;
    }

    protected function getTelegramToken(){
        return env('TELEGRAM_TOKEN');
    }
    protected function getTelegramUsername(){
        return env('TELEGRAM_USERNAME');
    }
    protected function getMysqlCredentials(): array
    {
        return [
            'host'     => env('DB_HOST'),
            'port'     => env('DB_PORT'),
            'user'     => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'database' => env('DB_DATABASE'),
        ];
    }
}
