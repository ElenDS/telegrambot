<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Score;

class ScoreRepository
{
    public function createNewScore(int $chatId){
        $score = new Score();
        $score->chat_id = $chatId;
        $score->total_score = 0;
        $score->save();
    }
    public function findScoreByChatId(int $chatId){
        return Score::where(['chat_id' => $chatId])->first();
    }
    public function addToScore(int $chatID, int $points){
        $score = $this->findScoreByChatId($chatID);
        $score->total_score += $points;
        $score->save();
    }
}
