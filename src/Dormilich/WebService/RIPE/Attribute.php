<?php
// Attribute.php

namespace Dormilich\WebService\RIPE;

class Attribute implements AttributeInterface
{
    const REQUIRED = true;

    const OPTIONAL = false;

    const MULTIPLE = true;

    const SINGLE   = false;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $value = [];

    /**
     * @var boolean
     */
    protected $mandatory;

    /**
     * @var boolean
     */
    protected $multiple;

    /**
     * Object constructor.
     * 
     * Note:
     *      While the last two parameters can be of any type, you’re 
     *      encouraged to use the class constants for better readability.
     * 
     * @param string $name Attribute name.
     * @param boolean $mandatory If the attribute is mandatory/required.
     * @param boolean $multiple If the attribute allows multiple values.
     * @return self
     */
    public function __construct($name, $mandatory, $multiple)
    {
        $this->name      = (string) $name;
        $this->mandatory = (bool) $mandatory;
        $this->multiple  = (bool) $multiple;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function isDefined()
    {
        return (count($this->value) > 0);
    }

    /**
     * @inheritDoc
     */
    public function isRequired()
    {
        return $this->mandatory;
    }

    /**
     * Whether the attribute allows multiple values.
     * 
     * @return boolean
     */
    public function isMultiple()
    {
        return $this->multiple;
    }

    /**
     * Convert attribute into a RIPE REST JSON compatible array.
     * At this place each attribute value is converted into a string.
     * 
     * @return array
     */
    public function toArray()
    {
        return array_map(function($value) {
            return [
                "name"  => $this->name, 
                "value" => $value, 
            ];
        }, $this->value);
    }

    /**
     * Get the current value(s) of the attribute.
     * If the value is unset NULL is returned, if the attribute
     * only allows a single value, that value is returned, otherwise an array.
     * 
     * @return mixed
     */
    public function getValue()
    {
        if (count($this->value) === 0) {
            return null;
        }

        if (!$this->multiple) {
            return $this->value[0];
        }

        return $this->value;
    }

    /**
     * Set the value(s) of the attribute. Each value must be either a scalar 
     * or a stringifiable object.
     * 
     * @param mixed $value A string or stringifyable object or an array thereof.
     * @return self
     * @throws InvalidDataTypeException Invalid data type of the value(s).
     */
    public function setValue($value)
    {
        $this->value = [];
        $this->addValue($value);

        return $this;
    }

    /**
     * Add value(s) to the attribute. If the attribute does not allow multiple 
     * values the value is replaced instead. The value(s) must be stringifiable.
     * If NULL is passed, execution is skipped. That is, `setValue(NULL)` will 
     * reset the Attribute while `addValue(NULL)` has no effect.
     * 
     * @param mixed $value A string or stringifyable object or an array thereof.
     * @return self
     * @throws InvalidDataTypeException Invalid data type of the value(s).
     */
    public function addValue($value)
    {
        if (NULL === $value) {
            return $this;
        }

        if (!$this->multiple) {
            $this->value = (array) $this->getStringValue($value);
            return $this;
        }

        foreach ((array) $value as $v) {
            $this->value[] = $this->getStringValue($v);
        }
 
        return $this;
    }

    /**
     * Converts a single value to a string. This method may be extended to add 
     * further value validation.
     * 
     * @param mixed $value A string or stringifyable object.
     * @return string Converted value.
     * @throws InvalidDataTypeException Invalid data type of the value(s).
     */
    protected function getStringValue($value)
    {
        if (true === $value) {
            return 'true';
        }
        if (false === $value) {
            return 'false';
        }
        if (is_scalar($value) or (is_object($value) and method_exists($value, '__toString'))) {
            return (string) $value;
        }

        $msg = sprintf('The [%s] attribute does not allow the %s data type.', $this->name, gettype($value));
        throw new InvalidDataTypeException($msg);
    }
}
