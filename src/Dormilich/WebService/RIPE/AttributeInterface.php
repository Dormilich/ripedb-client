<?php
// AttributeInterface.php

namespace Dormilich\WebService\RIPE;

interface AttributeInterface
{
    const REQUIRED = true;

    const OPTIONAL = false;

    const MULTIPLE = true;

    const SINGLE   = false;

    /**
     * Get the name of the attribute.
     * 
     * @return string
     */
    public function getName();

    /**
     * Whether the attribute is populated with data (i.e. not empty).
     * 
     * @return boolean
     */
    public function isDefined();

    /**
     * Whether the attribute is required/mandatory.
     * 
     * @return boolean
     */
    public function isRequired();

    /**
     * Whether the attribute allows multiple values.
     * 
     * @return boolean
     */
    public function isMultiple();

    /**
     * Get the value(s) of the attribute. Depending on the cardinality of the 
     * attribute this may be either an array of values or a single value.
     * 
     * @return mixed
     */
    public function getValue();

    /**
     * Set the value(s) of the attribute.
     * 
     * @param mixed $value A literal value (string preferred) or an array thereof.
     * @return self
     */
    public function setValue($value);

    /**
     * Add value(s) to the attribute. If the attribute does not allow multiple values
     * the value is replaced instead.
     * 
     * @param mixed $value A literal value (string preferred) or an array thereof.
     * @return self
     */
    public function addValue($value);
}