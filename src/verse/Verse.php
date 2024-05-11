<?php

namespace Memo\Verse;

/**
 * Represents a verse of the Bible.
 */
class Verse
{
    /**
     * Text of the verse.
     */
    private string $text;

    /**
     * Reference of the verse.
     */
    private string $reference;

    /**
     * Topic of the verse.
     */
    private string $topic;

    function __construct(string $text, string $reference, string $topic)
    {
        $this->text = $text;
        $this->reference = $reference;
        $this->topic = $topic;
    }

    /**
     * Returns the text of the verse.
     */
    function text(): string
    {
        return $this->text;
    }

    /**
     * Returns the reference of the verse.
     */
    function reference(): string
    {
        return $this->reference;
    }

    /**
     * Returns the topic of the verse.
     */
    function topic(): string
    {
        return $this->topic;
    }
}
