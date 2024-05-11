<?php

namespace Memo\Verse;

use Error;
use JsonSerializable;

/**
 * Represents the reference of a verse.
 */
class Reference implements JsonSerializable
{
    /**
     * The book.
     *
     * @var string
     */
    private string $book = "";

    /**
     * First chapter.
     *
     * @var int
     */
    private int $firstChapter = 0;

    /**
     * First verse.
     *
     * @var int
     */
    private int $firstVerse = 0;

    /**
     * Last chapter.
     *
     * @var integer|null
     */
    private ?int $lastChapter = null;

    /**
     * Last verse.
     *
     * @var integer|null
     */
    private ?int $lastVerse = null;

    /**
     * Constructor.
     *
     * @param string $refString The reference's string to parse.
     */
    function __construct(string $refString)
    {
        $matches = [];

        // Simple reference: e.g. "1 Peter 5:7"
        $pattern = '/^(?<book>[1-9]* *.+) +(?<firstChapter>[0-9]+) *: *(?<firstVerse>[0-9]+)$/';
        if (preg_match($pattern, $refString, $matches)) {
            $this->book = $matches['book'];
            $this->firstChapter = $matches['firstChapter'];
            $this->firstVerse = $matches['firstVerse'];
            return;
        }

        // Range reference: e.g. "Proverbs 4:5-6"
        $pattern = '/(?<book>[1-9]* *.+) +(?<chapter>[0-9]+):(?<firstVerse>[0-9]+)-(?<lastVerse>[0-9]+)/';
        if (preg_match($pattern, $refString, $matches)) {
            $this->book = $matches['book'];
            $this->firstChapter = $matches['chapter'];
            $this->firstVerse = $matches['firstVerse'];            
            $this->lastChapter = $matches['chapter'];
            $this->lastVerse = $matches['lastVerse'];
            return;
        }

        throw new Error("Invalid reference '$refString'");
    }

    function jsonSerialize()
    {
        return [
            "book" => $this->book,
            "firstVerse" => ["chapter" => $this->firstChapter, "verse" => $this->firstVerse],
            "lastVerse" => $this->lastChapter != null
                ? ["chapter" => $this->lastChapter, "verse" => $this->lastVerse]
                : null
        ];
    }
}
