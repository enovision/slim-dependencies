<?php
namespace Enovision\Slim\Dependencies\Classes;

class Response
{
    private $_hasDependencies;
    private $messages;
    private $delimiter;

    /**
     * Response constructor.
     * @param bool $_hasDependencies
     * @param array $messages
     * @param string $delimiter
     */
    function __construct($_hasDependencies = false, $messages = [], $delimiter = '<br/>') {
        $this->_hasDependencies = $_hasDependencies;
        $this->messages = $messages;
        $this->delimiter = $delimiter;
    }

    /**
     * Returns 'true' when it has dependencies
     * @return bool
     */
    function hasDependencies() {
        return $this->_hasDependencies > 0;
    }

    /**
     * Gets the messages in an array
     * @return array
     */
    function getMessages() {
        return $this->messages;
    }

    /**
     * Gets the messages in HTML format
     * @return string
     */
    function getHtmlMessages() {
        return implode($this->delimiter, $this->messages);
    }
}