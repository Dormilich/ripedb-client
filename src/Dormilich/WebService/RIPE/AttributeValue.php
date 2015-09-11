<?php
// AttributeValue.php

namespace Dormilich\WebService\RIPE;

use Dormilich\WebService\RIPE\Exceptions\InvalidDataTypeException;
use Dormilich\WebService\RIPE\Exceptions\InvalidValueException;

class AttributeValue
{
    protected $value = '';

    protected $comment;

    protected $type;

    protected $link;

    public function __construct($value)
    {
        $this->value = (string) $value;
    }

    public function __toString()
    {
        if (!empty($this->comment)) {
            return $this->value . ' # ' . $this->comment;
        }
        return $this->value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setComment($value)
    {
        $this->comment = (string) $value;

        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setType($value)
    {
        $this->type = (string) $value;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setLink($value)
    {
        $link = (string) $value;

        if (filter_var($link, \FILTER_VALIDATE_URL)) {
            $this->link = $link;
        }

        return $this;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function getObject()
    {
        if (!$this->type) {
            throw new InvalidDataTypeException('This attribute is not a referenced object.');
        }

        $class = __NAMESPACE__ . '\\RPSL\\' . $this->getClassName($this->type);

        if (!class_exists($class)) {
            throw new InvalidValueException('This object type does not exist.');
        }

        return new $class($this->value);
    }

    private function getClassName($type)
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $this->type)) );
    }
}
