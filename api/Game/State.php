<?php

namespace Memo\Game;

use Error;

/**
 * Represents the state of a game.
 */
class State
{
    /**
     * Random seed used to generate the game.
     *
     * @var integer
     */
    private int $seed;

    /**
     * The settings of the game.
     *
     * @var Settings
     */
    private Settings $settings;

    /**
     * Current question number.
     *
     * @var integer
     */
    private int $questionNumber;

    /**
     * Current player's streak.
     *
     * @var integer
     */
    private int $streak;

    /**
     * Number of answer attempts for the current question.
     *
     * @var integer
     */
    private int $attempts;

    /**
     * Indicates if the current question is answered. 
     *
     * @var boolean
     */
    private bool $questionIsAnswered;

    /**
     * Constructor.
     *
     * @param string|null $token The game state token. If null constructs a blank game state.
     */
    function __construct(?string $token = null)
    {
        $data = $token ? State::decodeToken($token) : null;

        $this->seed = $data['seed'] ?? random_int(0, 4294967296);
        $this->settings = new Settings($data['settings'] ?? null);
        $this->questionNumber = $data['questionNumber'] ?? 1;
        $this->streak = $data['streak'] ?? 0;
        $this->attempts = $data['attempts'] ?? 0;
        $this->questionIsAnswered = $data['questionIsAnswered'] ?? false;
    }

    /**
     * Encodes and returns the game state's token.
     *
     * @return string The token.
     */
    public function encodeToken(): string
    {
        return openssl_encrypt(
            json_encode([
                "seed" => $this->seed,
                "settings" => $this->settings,
                "questionNumber" => $this->questionNumber,
                "streak" => $this->streak,
                "attempts" => $this->attempts,
                "questionIsAnswered" => $this->questionIsAnswered
            ]),
            "AES-128-CTR",
            $_ENV['MEMO_GAME_STATE_TOKEN_SECRET'],
            0,
            $_ENV['MEMO_GAME_STATE_TOKEN_IV']
        );
    }

    /**
     * Decodes a game state token.
     *
     * @param string $token The token to decode.
     * @return array The token's data.
     */
    private static function decodeToken(string $token): array
    {
        try {
            $data = json_decode(openssl_decrypt(
                $token,
                "AES-128-CTR",
                $_ENV['MEMO_GAME_STATE_TOKEN_SECRET'],
                0,
                $_ENV['MEMO_GAME_STATE_TOKEN_IV']
            ), true);

            return [
                "seed" => State::decodeField($data, 'seed'),
                "settings" => State::decodeField($data, 'settings'),
                "questionNumber" => State::decodeField($data, 'questionNumber'),
                "streak" => State::decodeField($data, 'streak'),
                "attempts" => State::decodeField($data, 'attempts'),
                "questionIsAnswered" => State::decodeField($data, 'questionIsAnswered')
            ];
        } catch (Error $e) {
            throw new Error('Invalid token : ' . $e->getMessage());
        }
    }

    /**
     * Returns the seed of the game.
     *
     * @return integer The seed.
     */
    public function getSeed(): int
    {
        return $this->seed;
    }

    /**
     * Returns the question number.
     *
     * @return integer Question number.
     */
    public function getQuestionNumber(): int
    {
        return $this->questionNumber;
    }

    /**
     * Returns the game settings.
     *
     * @return Settings The settings.
     */
    public function getSettings(): Settings
    {
        return $this->settings;
    }

    /**
     * Returns the player's streak.
     *
     * @return integer Streak value.
     */
    public function getStreak(): int
    {
        return $this->streak;
    }

    /**
     * Returns the number of answer attempts for the current question.
     *
     * @return integer Attempts value.
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * Increments the number of answer attempts for the current question.
     *
     * @return void
     */
    public function incrementAttempts(): void
    {
        $this->attempts++;
    }

    /**
     * Incerments the question number.
     *
     * @return void
     */
    public function incrementQuestionNumber(): void
    {
        $this->questionNumber++;
        $this->streak = $this->questionIsAnswered ? $this->streak : 0;
        $this->attempts = 0;
        $this->questionIsAnswered = false;
    }

    /**
     * Indicates if the current question is answered.
     *
     * @return boolean True if the question is answered, false otherwise.
     */
    public function questionIsAnswered(): bool
    {
        return $this->questionIsAnswered;
    }

    /**
     * Sets the flag indicating if the current question is answered.
     *
     * @param boolean $isAnswered True if the question is answered, false otherwise.
     * @return void
     */
    public function setQuestionIsAnswered(bool $isAnswered): void
    {
        $this->questionIsAnswered = $isAnswered;
        $this->streak = $isAnswered ? $this->streak + 1 : $this->streak;
    }

    /**
     * Sets the settings of the game.
     *
     * @param Settings|null $settings The settings. If null, uses the default settings.
     * @return void
     */
    public function setSettings(?Settings $settings): void
    {
        $this->settings = $settings === null ? new Settings() : $settings;
    }

    /**
     * Decodes a field of a game state token.
     *
     * @param array $data The token's data.
     * @param string $field Name of the field.
     * @return mixed Value of the field. 
     */
    private static function decodeField(array $data, string $field)
    {
        if (!array_key_exists($field, $data)) {
            throw new Error("Missing field $field");
        }

        return $data[$field];
    }
}
