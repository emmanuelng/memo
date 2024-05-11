<?php

namespace Memo\Verse;

use Error;
use JsonSerializable;

/**
 * Represents a verse of the Bible.
 */
class Verse implements JsonSerializable
{
    /**
     * Builds a verse with JSON data.
     *
     * @param mixed $data The data.
     * @return Verse A new verse. If the data is invalid, throws an error.
     */
    public static function jsonDeserialize($data): Verse
    {
        try {
            return new Verse($data->text, $data->reference, $data->topic);
        } catch (Error $e) {
            throw new Error("Invalid verse: " . $e->getMessage());
        }
    }

    /**
     * Text of the verse.
     *
     * @var Text
     */
    private Text $text;

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
        $this->text = new Text($text);
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
