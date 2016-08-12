<?php

namespace ListOnce;

/**
 * Class Message
 *
 * A class representing a message returned from ListOnce
 *
 * @package ListOnce
 */
class Message
{
    /**
     * Whether this message represents a success or a failure
     *
     * @var bool|null
     */
    protected $success = null;

    /**
     * The text associated with this message
     *
     * @var string|null
     */
    protected $text = null;

    /**
     * Constructor
     *
     * @param bool|null $success
     * @param string|null $text
     */
    public function __construct($success = null, $text = null)
    {
        $this->success = $success;
        $this->text = $text;
    }

    /**
     * Get the success status of this message
     *
     * @return bool|null
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Get the text associated with this message
     *
     * @return string|null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get the text associated with this message
     *
     * @return string|null
     */
    public function __toString()
    {
        return $this->getText();
    }

    /**
     * Throw an exception if success is false
     *
     * @return void
     */
    public function throwException()
    {
        if ($this->success === false) {
            throw new \RuntimeException($this->text);
        }
    }
}