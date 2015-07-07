<?php
// FixedAttribute.php

namespace Dormilich\WebService\RIPE;

class FixedAttribute extends Attribute
{
    private $values = [];

    /**
     * Object constructor. Attributes with fixed values tend to be single-valued.
     * 
     * @param string $name Attribute name.
     * @param boolean $mandatory If the attribute is mandatory/required.
     * @param array $allowedValues Possible values for the attribute.
     * @return self
     */
    public function __construct($name, $mandatory, array $allowedValues)
    {
        parent::__construct($name, $mandatory, Attribute::SINGLE);
        $this->values = $allowedValues;
    }

    /**
     * Add a value comparison check before saving the value.
     * 
     * @param mixed $value Attribute value.
     * @return string Validated string value.
     * @throws UnexpectedValueException Value not allowed.
     */
    protected function getStringValue($value)
    {
        $value = parent::getStringValue($value);

        if (!in_array($value, $this->values, true)) {
            $msg = sprintf('Value "%" is not allowed for the [%s] attribute.', $value, $this->name);
            throw new \UnexpectedValueException($msg);
        }
        return $value;
    }
}
