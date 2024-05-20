<?php

namespace Memo\Game\Questions;

use Memo\Game\Question;
use Memo\Verse\Verse;
use Memo\Verse\Word;

/**
 * Missing words question.
 */
class MissingWordsQuestion implements Question
{
    /**
     * Verse of the question.
     *
     * @var Verse
     */
    private Verse $verse;

    /**
     * Words that are removed from the verse.
     *
     * @var array
     */
    private array $missingWords;

    /**
     * Number of found words.
     *
     * @var integer
     */
    private int $nbFoundWords;

    /**
     * Constructor.
     *
     * @param integer|null $seed Seed used to generate the question.
     */
    function __construct(?int $seed = null)
    {
        srand($seed);

        $this->verse = Verse::random();
        $this->missingWords = [];
        $this->nbFoundWords = 0;

        $this->initMissingWords();
    }

    function jsonSerialize()
    {
        return [
            "reference" => $this->verse->reference(),
            "fragments" => $this->getFragments()
        ];
    }

    public function submitAnswer($answer): bool
    {
        if (!is_array($answer)) {
            return false;
        }

        $this->nbFoundWords = 0;
        for ($i = 0; $i < sizeof($this->missingWords); $i++) {
            if (!array_key_exists($i, $answer)) {
                break;
            }
            
            if (!$this->missingWords[$i]->isSimilarTo($answer[$i])) {
                break;
            }
            
            $this->nbFoundWords++;
        }

        return $this->nbFoundWords >= sizeof($this->missingWords);
    }

    /**
     * Initializes the fragments of the verse.
     *
     * @return array Array of fragments.
     */
    private function getFragments(): array
    {
        $missingWordsIndices = array_map(function (Word $word) {
            return $word->index();
        }, $this->missingWords);

        $fragments      = [];
        $currentText    = '';
        $missingWordIdx = 0;

        for ($i = 0; $i < sizeof($this->verse->words()); $i++) {
            $word = $this->verse->words()[$i];

            if (!in_array($i, $missingWordsIndices)) {
                $currentText .= empty($currentText) || $word->length() === 1 ? '' :  ' ';
                $currentText .= $word->string();
                continue;
            }

            if ($currentText) {
                array_push($fragments, [
                    "type" => "text",
                    "text" => $currentText
                ]);
                $currentText = '';
            }

            array_push($fragments, [
                "type"   => "word",
                "text"   => ++$missingWordIdx <= $this->nbFoundWords ? $word->string() : null,
                "length" => $word->length()
            ]);
        }

        if ($currentText) {
            array_push($fragments, [
                "type" => "text",
                "text" => $currentText
            ]);
        }

        return $fragments;
    }

    /**
     * Initializes the missing words.
     *
     * @return void
     */
    private function initMissingWords(): void
    {
        $removableWords = array_filter($this->verse->words(), function ($word) {
            return MissingWordsQuestion::isWordRemovable($word);
        });

        // Remove between 50% and 80% of the removable words.
        $percentMissingWords = rand(50, 80) / 100;
        $nbWordsToRemove     = ceil(sizeof($removableWords) * $percentMissingWords);

        $indicesToRemove = array_rand($removableWords, $nbWordsToRemove);
        $indicesToRemove = is_array($indicesToRemove) ? $indicesToRemove : [$indicesToRemove];

        foreach ($indicesToRemove as $index) {
            array_push($this->missingWords, $removableWords[$index]);
        }
    }

    /**
     * Checks if a word can be removed from the verse or not.
     *
     * @param Word $word The word to check.
     * @return boolean True if the word can be removed, false otherwise.
     */
    public static function isWordRemovable(Word $word): bool
    {
        return $word->length() > 4;
    }
}
