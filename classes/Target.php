<?php
namespace Enovision\Slim\Dependencies\Classes;

use Sabre\Xml\Reader as Reader;

class Target
{
    public $table = null;
    public $field = null;
    public $field2 = null;
    public $field3 = null;
    public $field4 = null;
    public $group = null;
    public $alias = null;
    public $where = null;

    public function __get($property)
    {
        return isset($this->$property) ? $this->$property : null;
    }

    public function __construct($child)
    {
        foreach ($child as $el) {

            if ($el['name'] === '{http://example.org/dependencies}table') {
                $this->table = $el['value'];
            } elseif ($el['name'] === '{http://example.org/dependencies}field') {
                $this->field = $el['value'];
            } elseif ($el['name'] === '{http://example.org/dependencies}field2') {
                $this->field2 = $el['value'];
            } elseif ($el['name'] === '{http://example.org/dependencies}field3') {
                $this->field3 = $el['value'];
            } elseif ($el['name'] === '{http://example.org/dependencies}field4') {
                $this->field4 = $el['value'];
            } elseif ($el['name'] === '{http://example.org/dependencies}group') {
                $this->group = $el['value'];
            } elseif ($el['name'] === '{http://example.org/dependencies}alias') {
                $this->alias = $el['value'];
            } elseif ($el['name'] === '{http://example.org/dependencies}where') {
                $this->where = $el['value'];
            }
        }

        return $this;

    }
}