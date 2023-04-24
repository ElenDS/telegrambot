<?php

declare(strict_types=1);

namespace App\Services\Telegram;

class TelegramQuizService
{
    protected int $result = 0;

    /**
     * @return int
     */
    public function getResult(): int
    {
        return $this->result;
    }

    /**
     * @param int $result
     */
    public function setResult(int $result): void
    {
        $this->result = $result;
    }

    public function makeExpression(): array
    {
        $valueArray = [rand(0, 9), rand(0, 9)];
        $this->setResult($valueArray[0] * $valueArray[1]);
        return $valueArray;
    }
}
