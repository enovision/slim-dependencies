<?php
namespace Enovision\Slim\Dependencies\Classes;

use Enovision\Slim\Dependencies\Classes\Table as clsTable;
use Sabre\Xml\Reader as Reader;

class Dependencies implements \Sabre\Xml\XmlDeserializable
{

    public $schemas = [];

    static function xmlDeserialize(Reader $reader)
    {
        $dependencies = new self();

        $children = $reader->parseInnerTree();

        foreach ($children as $child) {

            if ($child['value'] instanceof clsTable) {

                $dependencies->schemas[] = $child['value'];
            }
        }

        return $dependencies;

    }

    public function getSchema($schema = '') {

        foreach ($this->schemas as $sch) {
            if ($sch->source === $schema) {
                return $sch;
            }
        }

        return false;
    }

    public function getSchemaTargets($schema = '') {
        $sch = $this->getSchema($schema);
        return $sch !== false && isset($sch->targets) ? $sch->targets : null;
    }

    public function getSchemaSource($schema = '') {
        $sch = $this->getSchema($schema);
        return $sch !== false && isset($sch->source) ? $sch->source : null;
    }
}