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
     * List of all verses.
     *
     * @var array|null
     */
    private static ?array $all = null;

    /**
     * Returns a random verse.
     *
     * @return Verse A verse.
     */
    public static function random(): Verse
    {
        $all = Verse::all();
        return $all[rand(0, sizeof($all) - 1)];
    }

    /**
     * Returns the list of all verse topics.
     *
     * @return array A list of strings.
     */
    public static function topics(): array
    {
        $topics = [];

        foreach (Verse::all() as $verse) {
            if (!in_array($verse->topic, $topics)) {
                array_push($topics, $verse->topic);
            }
        }

        return $topics;
    }

    /**
     * Returns the list of all verses.
     * 
     * @return array List of verses.
     */
    public static function all(): array
    {
        if (Verse::$all === null) {
            $verses = json_decode(file_get_contents(__DIR__ . '/../../verses.json'));
            Verse::$all = [];

            foreach ($verses as $verse) {
                array_push(Verse::$all, Verse::jsonDeserialize($verse));
            }
        }

        return Verse::$all;
    }

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

    /**
     * Returns the reference of the verse.
     *
     * @return Reference The reference.
     */
    public function reference(): Reference
    {
        return $this->reference;
    }

    /**
     * Returns the text of the verse.
     *
     * @return Text The text.
     */
    public function text(): Text
    {
        return $this->text;
    }

    /**
     * Returns the topic of the verse.
     *
     * @return string The topic.
     */
    public function topic(): string {
        return $this->topic;
    }

    /**
     * Returns the words of the verse.
     *
     * @return array An array of Word objects.
     */
    function words(): array
    {
        return $this->text->words();
    }
}
