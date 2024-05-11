<?php

namespace Memo\Verse;

use JsonSerializable;

/**
 * Represents a verse of the Bible.
 */
class Verse implements JsonSerializable
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

    function jsonSerialize()
    {
        return [
            "text" => $this->text,
            "reference" => $this->reference,
            "topic" => $this->topic
        ];
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
