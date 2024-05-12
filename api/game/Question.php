<?php

namespace Memo\Game;

use JsonSerializable;

/**
 * Represents a question of a game.
 */
interface Question extends JsonSerializable
{
    /**
     * Submits an answer to the question.
     *
     * @param mixed $answer The answer.
     * @return boolean True if the answer is correct, false otherwise.
     */
    public function submitAnswer(mixed $answer): bool;
}
