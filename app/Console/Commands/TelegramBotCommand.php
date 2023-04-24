<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Telegram\LongmanTelegramBotClient;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;

class TelegramBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:telegram-bot-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(LongmanTelegramBotClient $telegramBotClient, TelegramBotService $telegramBot)
    {
        try {
            $telegram = $telegramBotClient->createApiObject();
        } catch (TelegramException $exception) {
            echo $exception->getMessage();
            exit;
        }

        while (true) {
            $updates = $telegram->handleGetUpdates()->getResult();
            if (!empty($updates)) {
                $telegramBot->handler($updates);
            } else {
                sleep(1);
            }
        }
    }
}
