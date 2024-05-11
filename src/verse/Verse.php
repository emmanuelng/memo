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
     *
     * @var string
     */
    private string $text;

    /**
     * Reference of the verse.
     *
     * @var Reference
     */
    private Reference $reference;

    /**
     * Topic of the verse.
     *
     * @var string
     */
    private string $topic;

    /**
     * Constructor.
     *
     * @param string $text Text of the verse.
     * @param string $reference Reference of the verse.
     * @param string $topic Topic of the verse.
     */
    function __construct(string $text, string $reference, string $topic)
    {
        $this->text = $text;
        $this->reference = new Reference($reference);
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
}
