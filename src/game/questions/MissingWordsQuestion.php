<?php

namespace Memo\Game\Questions;

use Memo\Game\Question;

/**
 * Missing words question.
 */
class MissingWordsQuestion implements Question
{
    function jsonSerialize()
    {
        return null;
    }

    public function submitAnswer($answer): bool
    {
        return false;
    }
}
