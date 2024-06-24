<?php

namespace Memo\Game;

use Error;
use JsonSerializable;
use Memo\Game\Questions\MissingWordsQuestion;
use Memo\Game\Questions\ReferenceMultipleChoice;
use Memo\Verse\Verse;
use ReflectionClass;

/**
 * Represents a game.
 */
class Game implements JsonSerializable
{
    /**
     * Creates a new game.
     *
     * @param Settings|null $settings The game settings.
     * @return Game A new game.
     */
    public static function create(?Settings $settings = null): Game
    {
        $state = new State();
        $state->setSettings($settings);
        return new Game($state);
    }

    /**
     * Recovers a game from a token.
     *
     * @param string $token The token.
     * @return Game The recovered game.
     */
    public static function recover(string $token): Game
    {
        return new Game(new State($token));
    }

    /**
     * State of the game.
     *
     * @var State
     */
    private State $state;

    /**
     * The current verse.
     *
     * @var Verse|null
     */
    private ?Verse $verse;

    /**
     * The current question.
     *
     * @var Question|null
     */
    private ?Question $question;

    /**
     * Array containing all the possible verse.
     *
     * @var array
     */
    private array $verses;

    /**
     * List of verses that were used for the previous questions.
     *
     * @var array
     */
    private array $previousVerses;

    /**
     * Constructor.
     * 
     * @param State|null $state The game state.
     */
    private function __construct(?State $state = null)
    {
        $this->state = $state !== null ? $state : new State();
        $this->question = null;
        $this->verses = [];
        $this->previousVerses = [];

        // Build the list of all verses.
        $settings = $this->state->getSettings();
        $topics   = $settings->getTopics();

        foreach (Verse::all() as $verse) {
            if (in_array($verse->topic(), $topics)) {
                array_push($this->verses, $verse);
            }
        }
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

        srand($this->state->getSeed());

        for ($i = 0; $i < $this->state->getQuestionNumber(); $i++) {
            $this->updateQuestion();
        }

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
        $this->verse = null;
    }

    private function updateQuestion(): void
    {
        $this->updateVerse();
        if (!$this->verse) {
            $this->question = null;
            return;
        }

        $questions = [
            function (Verse $verse, int $seed): Question {
                return new MissingWordsQuestion($verse, $seed);
            },
            function (Verse $verse, int $seed): Question {
                return new ReferenceMultipleChoice($verse, $seed);
            }
        ];

        $index = rand(0, sizeof($questions) - 1);
        $seed  = rand(0, 4294967296);
        $this->question = $questions[$index]($this->verse, $seed);
    }

    private function updateVerse(): void
    {
        // If there is no verse, set verse to null. If there is one verse only,
        // use it.
        if (sizeof($this->verses) <= 1) {
            $this->verse = empty($this->verses) ? null : $this->verses[0];
            return;
        }

        // Pick a random verse. If the verse was already used, pick another one.
        $verse = $this->verses[rand(0, sizeof($this->verses) - 1)];
        if (in_array($verse, $this->previousVerses)) {
            $this->updateVerse();
            return;
        }

        // Add the verse to the list of used verses.
        $this->verse = $verse;
        array_push($this->previousVerses, $this->verse);

        // If we reach a certain number of used verses, forget the oldest one,
        // so it can be picked once again.
        $slidingWindow = min(5, sizeof($this->verses) - 1);
        if (sizeof($this->previousVerses) >= $slidingWindow) {
            array_shift($this->previousVerses);
        }
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
