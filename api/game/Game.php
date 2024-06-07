<?php

namespace Memo\Game;

use Error;
use JsonSerializable;
use Memo\Game\Questions\MissingWordsQuestion;
use Memo\Game\Questions\ReferenceMultipleChoice;
use ReflectionClass;

/**
 * Represents a game.
 */
class Game implements JsonSerializable
{
    /**
     * State of the game.
     *
     * @var State
     */
    private State $state;

    /**
     * The current question.
     *
     * @var Question|null
     */
    private ?Question $question;

    /**
     * Constructor.
     *
     * @param string|null $state Game state token. If null creates a blank game.
     */
    function __construct(?string $state = null)
    {
        $this->state = new State($state);
        $this->question = null;
    }

    function jsonSerialize()
    {
        return [
            'token' => $this->state->encodeToken(),
            'questionType' => (new ReflectionClass($this->getQuestion()))->getShortName(),
            'isCorrect' => $this->state->questionIsAnswered(),
            'streak' => $this->state->getStreak(),
            'canAnswer' => $this->canAnswer(),
            'question' => $this->getQuestion()
        ];
    }

    /**
     * Returns the current question.
     *
     * @return Question|null The current question.
     */
    public function getQuestion(): ?Question
    {
        if ($this->question) {
            return $this->question;
        }

        $questions = [
            function (int $seed): Question {
                return new MissingWordsQuestion($seed);
            },
            function (int $seed): Question {
                return new ReferenceMultipleChoice($seed);
            }
        ];

        srand($this->state->getSeed());

        $index = 0;
        $seed  = 0;

        for ($i = 0; $i < $this->state->getQuestionNumber(); $i++) {
            $index = rand(0, sizeof($questions) - 1);
            $seed  = rand(0, 4294967296);
        }

        $this->question = $questions[$index]($seed);
        return $this->question;
    }

    /**
     * Moves to the next question.
     *
     * @return void
     */
    public function next(): void
    {
        $this->state->incrementQuestionNumber();
        $this->question = null;
    }

    /**
     * Checks if the player can still answer to the current question.
     *
     * @return boolean True if the player can answer, false otherwise.
     */
    private function canAnswer(): bool
    {
        $question = $this->getQuestion();
        if ($question == null) {
            return false;
        }

        $maxAttempts = $question->maxAnswerAttempts();
        if ($maxAttempts == null)
            return true;

        return $this->state->getAttempts() < $maxAttempts;
    }

    /**
     * Submits an answer for the current question.
     *
     * @param mixed $answer The answer.
     * @return void
     */
    public function submitAnswer($answer): void
    {
        $question = $this->getQuestion();
        if ($question == null) {
            return;
        }

        $maxAttempts = $question->maxAnswerAttempts();
        if ($maxAttempts != null && $this->state->getAttempts() >= $maxAttempts) {
            throw new Error("Too many answers.", 400);
        }

        $this->state->incrementAttempts();

        if ($question->submitAnswer($answer)) {
            $this->state->setQuestionIsAnswered(true);
        }
    }
}
