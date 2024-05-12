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

        $this->seed = $data ? $data['seed'] : random_int(0, 4294967296);
        $this->questionNumber = $data ? $data['questionNumber'] : 1;
        $this->streak = $data ? $data['streak'] : 0;
        $this->questionIsAnswered = $data ? $data['questionIsAnswered'] : false;
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
                "questionNumber" => $this->questionNumber,
                "streak" => $this->streak,
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
                "questionNumber" => State::decodeField($data, 'questionNumber'),
                "streak" => State::decodeField($data, 'streak'),
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
     * Returns the player's streak.
     *
     * @return integer Streak value.
     */
    public function getStreak(): int
    {
        return $this->streak;
    }

    /**
     * Incerments the question number.
     *
     * @return void
     */
    public function incrementQuestionNumber(): void
    {
        $this->questionNumber++;
        $this->questionIsAnswered = false;
        $this->resetStreak();
    }

    /**
     * Increments the player's streak.
     *
     * @return void
     */
    public function incrementStreak(): void
    {
        $this->streak++;
    }

    /**
     * Resets the player's streak.
     *
     * @return void
     */
    public function resetStreak(): void
    {
        $this->streak = 0;
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
