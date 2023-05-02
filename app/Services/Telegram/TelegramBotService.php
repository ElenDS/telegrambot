<?php

declare(strict_types=1);

namespace App\Services\Telegram;

use App\Repositories\QuizRepository;
use App\Repositories\ScoreRepository;
use Longman\TelegramBot\Request;

class TelegramBotService
{
    public function __construct(
        protected TelegramQuizService $telegramQuiz,
        protected QuizRepository $quizRepository,
        protected ScoreRepository $scoreRepository
    ) {
    }

    public function handler($updates): void
    {
        foreach ($updates as $update) {
            $chatId = intval($update->getMessage()->getChat()->getId());
            $text = $update->getMessage()->getText();

            match (true) {
                $text === TelegramBotCommands::START => $this->startNewQuiz($chatId),
                $text === TelegramBotCommands::GET_SCORE => $this->getScore($chatId),
                boolval(preg_match('/\d+/', $text)) => $this->respondToQuizAnswer($chatId, intval($text)),
                default => $this->respondToInvalidCommand($chatId)
            };
        }
    }

    protected function startNewQuiz(int $chatId): void
    {
        $this->quizRepository->deleteQuiz($chatId);

        $expression = $this->telegramQuiz->makeExpression();

        $this->quizRepository->createNewQuiz($chatId, $this->telegramQuiz->getResult());

        if (!$this->scoreRepository->findScoreByChatId($chatId)) {
            $this->scoreRepository->createNewScore($chatId);
        }

        Request::sendMessage($this->createRespondParams($chatId, $expression));
    }

    protected function createRespondParams(int $chatId, array $quiz): array
    {
        $randomResults = [$quiz[0] * $quiz[1], rand(0, 100), rand(0, 100), rand(0, 100)];
        shuffle($randomResults);
        $replyMarkup = [
            'keyboard' => [
                [
                    ['text' => $randomResults[0]],
                    ['text' => $randomResults[1]]
                ],
                [
                    ['text' => $randomResults[2]],
                    ['text' => $randomResults[3]]
                ]
            ],
            'resize_keyboard' => true
        ];

        return [
            'chat_id' => $chatId,
            'text' => $quiz[0] . ' * ' . $quiz[1],
            'reply_markup' => json_encode($replyMarkup)
        ];
    }

    protected function getScore(int $chatId): void
    {
        $this->quizRepository->deleteQuiz($chatId);

        $score = $this->scoreRepository->findScoreByChatId($chatId);

        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Your score is ' . $score->total_score
        ]);
    }

    protected function respondToQuizAnswer(int $chatId, int $result): void
    {
        $quiz = $this->quizRepository->findQuizByChatId($chatId);
        if ($quiz->result === $result) {
            $points = TelegramQuizPoints::CORRECT;
        } else {
            $points = TelegramQuizPoints::INCORRECT;
        }

        $this->scoreRepository->addToScore($chatId, $points);

        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => 'You`ve got' . $points . ' points'
        ]);

        $this->startNewQuiz($chatId);
    }

    protected function respondToInvalidCommand(int $chatId): void
    {
        $this->quizRepository->deleteQuiz($chatId);

        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => 'Something was wrong! Write /start again!'
        ]);
    }
}
