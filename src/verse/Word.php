<?php

namespace Memo\Verse;

use JsonSerializable;

/**
 * Represents a word of a verse.
 */
class Word implements JsonSerializable
{
    /**
     * String of the word.
     *
     * @var string
     */
    private string $string;

    /**
     * Constructor.
     *
     * @param string $string String of the word.
     */
    function __construct(string $string)
    {
        $this->string = $string;
    }

    function jsonSerialize()
    {
        return $this->string;
    }

    /**
     * Checks if the word matches exactly a string.
     *
     * @param string $string The string.
     * @return bool True if the string is exactly equal to the word.
     */
    function equals(string $string): bool
    {
        return $string === $this->string;
    }
}
