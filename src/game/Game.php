<?php

namespace Memo\Game;

use JsonSerializable;
use Memo\Game\Questions\MissingWordsQuestion;
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
        ];

        srand($this->state->getSeed());

        $index = 0;
        $seed  = 0;

        for ($i = 0; $i < $this->state->getQuestionNumber(); $i++) {
            $index = random_int(0, sizeof($questions) - 1);
            $seed  = random_int(0, 4294967296);
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
     * Submits an answer for the current question.
     *
     * @param mixed $answer The answer.
     * @return void
     */
    public function submitAnswer($answer): void
    {
        $isCorrect = $this->getQuestion()->submitAnswer($answer);
        $this->state->setQuestionIsAnswered($isCorrect);

        if ($isCorrect) {
            $this->state->incrementStreak();
        } else {
            $this->state->resetStreak();
        }
    }
}
