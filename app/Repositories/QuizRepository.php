<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Quiz;

class QuizRepository
{
    public function createNewQuiz(int $chatId, int $result): void
    {
        $quiz = new Quiz();
        $quiz->chat_id = $chatId;
        $quiz->result = $result;
        $quiz->save();
    }
    public function findQuizByChatId(int $chatId){
        return Quiz::where(['chat_id' => $chatId])->first();
    }
    public function deleteQuiz(int $chatId): void
    {
        $quiz = $this->findQuizByChatId($chatId);
        if($quiz){
            $quiz->delete();
        }
    }
}
