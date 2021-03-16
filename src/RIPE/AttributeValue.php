<?php
// AttributeValue.php

namespace Dormilich\WebService\RIPE;

use Dormilich\WebService\RIPE\Exceptions\InvalidDataTypeException;
use Dormilich\WebService\RIPE\Exceptions\InvalidValueException;

class AttributeValue
{
    /**
     * @var string
     */
    protected $value = '';

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var string RPSL object type.
     */
    protected $type;

    /**
     * @var string RIPE DB URL.
     */
    protected $link;

    /**
     * Create value object from attribute value string.
     * 
     * @param string $value Attribute value string.
     * @return self
     */
    public function __construct($value)
    {
        $this->value = (string) $value;
    }

    /**
     * Return the string value of the attribute. If a comment is set, 
     * append the comment to the value.
     * 
     * @return string
     */
    public function __toString()
    {
        if (!empty($this->comment)) {
            return $this->value . ' # ' . $this->comment;
        }
        return $this->value;
    }

    /**
     * Get the bare attribute value.
     * 
     * @return type
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the comment string.
     * 
     * @param string $value Comment
     * @return self
     */
    public function setComment($value)
    {
        $this->comment = (string) $value;

        return $this;
    }

    /**
     * Get the comment string.
     * 
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set the object type. Only makes sense if the attribute is a reference 
     * to another RIPE DB object.
     * 
     * @param string $value One of the types of the RPSL objects.
     * @return self
     */
    public function setType($value)
    {
        $this->type = (string) $value;

        return $this;
    }

    /**
     * Get the object type.
     * 
     * @return string The set object type or NULL if not set.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the RIPE DB URL of the referenced object.
     * 
     * @param string $value RIPE DB URL.
     * @return self
     */
    public function setLink($value)
    {
        $link = (string) $value;

        if (filter_var($link, \FILTER_VALIDATE_URL)) {
            $this->link = $link;
        }

        return $this;
    }

    /**
     * Get the reference URL.
     * 
     * @return string RIPE DB URL or NULL if not set.
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Get an RPSL object for lookup purposes (i.e. only the primary loopup key is set).
     * 
     * @return Object Empty RPSL object.
     * @throws InvalidDataTypeException Attribute is not a referenced object.
     * @throws InvalidValueException Invalid object type.
     */
    public function getObject()
    {
        $class = $this->getClassName();

        if (!class_exists($class)) {
            throw new InvalidValueException('This object type does not exist.');
        }

        return new $class($this->value);
    }

    /**
     * Convert object type into object class name.
     * 
     * @return string
     * @throws InvalidDataTypeException Attribute is not a referenced object.
     */
    private function getClassName()
    {
        if (!$this->type) {
            throw new InvalidDataTypeException('This attribute is not a referenced object.');
        }

        $class = str_replace(' ', '', ucwords(str_replace('-', ' ', $this->type)) );
        return __NAMESPACE__ . '\\RPSL\\' . $class;
    }
}
