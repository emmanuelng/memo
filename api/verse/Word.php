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
     * Index of the word in the verse.
     *
     * @var integer
     */
    private int $index;

    /**
     * Constructor.
     *
     * @param string $string String of the word.
     * @param int    $index  Index of the word in the verse.
     */
    function __construct(string $string, int $index)
    {
        $this->string = $string;
        $this->index  = $index;
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
    public function equals(string $string): bool
    {
        return $string === $this->string;
    }

    /**
     * Checks if this word is similar to a string (loose comparison).
     *
     * @param string $string The string.
     * @return boolean True if this word is similar, false otherwise.
     */
    public function isSimilarTo(string $string) : bool
    {
        return Word::normalize($string) === Word::normalize($this->string);
    }

    /**
     * Returns the length of the word.
     *
     * @return integer Length of the word.
     */
    public function length(): int
    {
        return strlen($this->string);
    }

    /**
     * Returns the index of the word in the verse.
     *
     * @return integer Index of word in the verse.
     */
    public function index(): int
    {
        return $this->index;
    }

    /**
     * Returns the string of the word.
     *
     * @return string String of the word.
     */
    public function string(): string
    {
        return $this->string;
    }

    /**
     * Normalizes a string. This process removes upper case characters, spaces and
     * non-alphanumeric characters.
     *
     * @param string $string The string to normalize.
     * @return string The normalized string.
     */
    private static function normalize(string $string): string
    {
        $normalized = $string;

        $normalized = strtolower($normalized);
        $normalized = strtr($normalized,'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ','aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        $normalized = preg_replace('/[\u0300-\u036f]/', '', $normalized);
        $normalized = preg_replace('/[^a-zA-Z0-9]/', '', $normalized);

        return $normalized;
    }
}
