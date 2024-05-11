<?php

namespace Memo\Verse;

use JsonSerializable;

/**
 * Represents the text of a verse.
 */
class Text implements JsonSerializable
{
    /**
     * String of the text.
     *
     * @var string
     */
    private string $string;

    /**
     * Words of the text.
     *
     * @var array
     */
    private array $words;

    /**
     * Constructor.
     *
     * @param string $string String of the text.
     */
    function __construct(string $string)
    {
        $this->string = $string;
        $this->words = Text::tokenize($string);
    }

    function jsonSerialize()
    {
        return $this->string;
    }

    /**
     * Returns the words of the text.
     *
     * @return array
     */
    function words(): array
    {
        return $this->words;
    }

    /**
     * Tokenizes a string, i.e. separates it in words.
     *
     * @param string $string The string to tokenize.
     * @return array An array of strings representing the words of the string.
     */
    private static function tokenize(string $string): array
    {
        if (empty($string)) {
            return [];
        }

        $word = '';
        foreach (mb_str_split($string) as $char) {
            if (Text::wordIsComplete($word, $char)) {
                break;
            }

            $word .= $char;
        }

        $remaining = substr($string, strlen($word));
        $word = trim($word);

        return array_merge($word ? [new Word($word)] : [], Text::tokenize($remaining));
    }

    /**
     * Checks if a word is complete.
     *
     * @param string $word The word.
     * @param string $nextChar The next character.
     * @return boolean True if the word is complete, false otherwise.
     */
    private static function wordIsComplete(string $word, string $nextChar): bool
    {
        if (empty($word)) {
            return false;
        }

        return (
            $nextChar === ' ' ||
            $nextChar === ',' ||
            $nextChar === ';' ||
            $nextChar === '.'
        );
    }
}
