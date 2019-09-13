<?php
namespace Enovision\Slim\Dependencies\Classes;

use Enovision\Slim\Dependencies\Classes\Target as clsTarget;
use Sabre\Xml\Reader as Reader;

class Table implements \Sabre\Xml\XmlDeserializable
{
    public $source;
    public $targets = [];

    static function xmlDeserialize(Reader $reader)
    {
        $table = new self();

        // Borrowing a parser from the KeyValue class.
        $keyValue = \Sabre\Xml\Element\KeyValue::xmlDeserialize($reader);

        if (isset($keyValue['{http://example.org/dependencies}source'])) {
            $table->source = $keyValue['{http://example.org/dependencies}source'];
        }

        if (isset($keyValue['{http://example.org/dependencies}targets'])) {

            foreach ($keyValue['{http://example.org/dependencies}targets'] as $child) {
                $target = new clsTarget($child['value']);
                $table->targets[] = $target;
            }
        }

        return $table;
    }
}