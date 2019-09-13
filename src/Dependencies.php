<?php
namespace Enovision\Slim\Dependencies;

use Sabre\Xml\Reader as Reader;
use Sabre\Xml\Deserializer\keyValue as keyValue;
use Sabre\Xml\Service as Service;

use Enovision\Slim\Dependencies\Classes\Dependencies as clsDependencies;
use Enovision\Slim\Dependencies\Classes\Table as clsTable;
use Enovision\Slim\Dependencies\Classes\Target as clsTarget;
use Enovision\Slim\Dependencies\Classes\Response as clsResponse;

class Dependencies
{
    private $container = null;
    private $language = 'en';
    private $languagePath = null;
    private $languageTexts = [];
    private $messages = [];
    private $xmlPath = null;
    private $error = false;
    private $delimiter = null;
    private $fnCallback = null;

    function __construct($container, $xmlPath, $fnCallback, $language = 'en', $languagePath = null, $delimiter = '<br/>')
    {
        $this->container = $container;
        $this->language = $language;
        $this->fnCallback = $fnCallback;
        $this->xmlPath = $xmlPath;
        $this->delimiter = $delimiter;
        $this->loadDependencies();
        $this->loadLanguageTexts($language, $languagePath);
    }

    private function loadDependencies()
    {
        $filename = $this->xmlPath;

        if (file_exists($filename) && is_readable($filename)) {
            $contents = file_get_contents($filename);
        } else {
            $this->error = true;
        }

        if ($this->error) return false;

        $service = new Service();
        $service->elementMap = [
            '{http://example.org/dependencies}dependencies' => clsDependencies::class,
            '{http://example.org/dependencies}schema' => clsTable::class
        ];

        $this->dependencies = $service->parse($contents);
        unset($contents);
    }

    public function hasError()
    {
        return $this->error;
    }

    //--------------------------------------------------------------------------
    // CrossCheck - Cross reference check before deleting a record
    // Parameters in:
    //  - source table (fm_form)
    //  - value (value of the unique key (hash)
    //--------------------------------------------------------------------------

    function Check($schema = false, $group = null, $val = false, $val2 = null, $val3 = null)
    {
        $targets = $this->dependencies->getSchemaTargets($schema);

        $this->messages = [];

        if ($targets === false || count($targets) === 0) {
            return new clsResponse(true);
        }

        if (!is_callable($this->fnCallback)) {
            $this->messages[] = $this->getMessageText('callback');
            return new clsResponse(false, $this->messages);
        }

        foreach ($targets as $target) {

            $callback = $this->fnCallback;
            $result = $callback($target, $group, $val, $val2, $val3);

            if ($result > 0) {
                $msg = $result === 1 ? 'single' : 'multi';
                $this->messages[] = $this->getMessageText($msg, ['count' => $result, 'table' => $target->alias]);
            }
        }

        if (count($this->messages) > 0) {
            $this->messages[] = $this->getMessageText('clear-first');
        }

        return new clsResponse(count($this->messages), $this->messages);
    }

    /**
     * Load language text from the language file
     * @param string $language
     * @param string $languagePath
     */
    private function loadLanguageTexts($language, $languagePath)
    {
        $this->languagePath = empty($languagePath) ? dirname(__FILE__, 2) . '/locale/' . $language . '/lang.php' : $language;

        if (file_exists($this->languagePath)) {
            $this->languageTexts = include($this->languagePath);
        }
    }

    /**
     * Get the message text based on a message-id
     * @param string $msg
     * @param array $subst
     * @return string $message
     */
    private function getMessageText($msg, $subst = [])
    {
        $message = $this->languageTexts[$msg];

        foreach ($subst as $ix => $sub) {
            $message = str_replace('&' . $ix, $sub, $message);
        }

        return $message;
    }

    public function showTableDep($schema = '')
    {
        return $this->dependencies->getSchemaTargets($schema);
    }

}
