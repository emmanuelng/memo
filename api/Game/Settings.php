<?php

namespace Memo\Game;

use JsonSerializable;
use Memo\Verse\Verse;

/**
 * Class representing the settings of a game.
 */
class Settings implements JsonSerializable
{
    /**
     * List of allowed verse topics.
     *
     * @var array
     */
    private array $topics;

    /**
     * Constructor.
     *
     * @param any $json Either a JSON string representing the settings or an
     *                  associative array built from a parsed JSON.
     */
    function __construct($json = null)
    {
        $this->jsonDeserialize($json);
    }

    /**
     * Returns the list of allowed topics.
     *
     * @return array A list of strings.
     */
    public function getTopics(): array
    {
        return $this->topics;
    }

    /**
     * Deserializes settings from a JSON object.
     *
     * @param any $json The JSON object.
     * @return Settings An instance of the settings class.
     */
    public function jsonDeserialize($json): void
    {
        $data = is_array($json) ? $json : json_decode($json ?? "", true);
        $this->setTopics($data['topics'] ?? null);
    }

    function jsonSerialize()
    {
        return [
            'topics' => $this->topics
        ];
    }

    private function setTopics(?array $topics): void
    {
        $this->topics = [];

        if ($topics === null) {
            $this->topics = Verse::topics();
            return;
        }

        foreach (Verse::topics() as $topic) {
            if (in_array($topic, $topics)) {
                array_push($this->topics, $topic);
            }
        }
    }
}
