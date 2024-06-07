<?php

namespace Memo\Game\Questions;

use Memo\Game\Question;
use Memo\Verse\Reference;
use Memo\Verse\Verse;

class ReferenceMultipleChoice implements Question
{
    /**
     * Number of choices to generate.
     */
    const NB_CHOICES = 3;

    /**
     * Verse of the question.
     *
     * @var Verse
     */
    private Verse $verse;

    /**
     * Array containing the reference of the verse and two other random references.
     *
     * @var array
     */
    private array $referenceChoices;

    /**
     * Indicates if an answer (true or false) was submitted.
     *
     * @var boolean
     */
    private bool $answerSubmitted;

    /**
     * Constructor.
     *
     * @param integer|null $seed Seed used to generate the question.
     */
    function __construct(?int $seed = null)
    {
        srand($seed);

        $this->verse = Verse::random();
        $this->answerSubmitted = false;
        $this->referenceChoices = $this->initChoices();
    }

    /**
     * Generates an array of multiple references tfrom which the player can choose.
     *
     * @return array Array of references.
     */
    function initChoices(): array
    {
        $choices = [];
        array_push($choices, $this->verse->reference());

        for ($i = 0; $i < ReferenceMultipleChoice::NB_CHOICES - 1; $i++) {
            array_push($choices, Verse::random()->reference());
        }

        shuffle($choices);
        return $choices;
    }

    function jsonSerialize()
    {
        return [
            "text" => $this->verse->text(),
            "choices" => array_map(function (Reference $reference) {
                return [
                    "reference" => $reference,
                    "isAnswer" => $this->answerSubmitted
                        ? $reference == $this->verse->reference()
                        : null
                ];
            }, $this->referenceChoices)
        ];
    }

    public function maxAnswerAttempts(): ?int
    {
        return 1;
    }

    function submitAnswer($answer): bool
    {
        /* 
         * The answer must be an integer which represents the index of the answer (0 -> first
         * reference).
        */

        if (!is_int($answer) || $answer < 0 || $answer >= sizeof($this->referenceChoices)) {
            return false;
        }

        $this->answerSubmitted = true;

        $userAnswer = $this->referenceChoices[$answer];
        $realAnswer = $this->verse->reference();

        return $userAnswer == $realAnswer;
    }
}
