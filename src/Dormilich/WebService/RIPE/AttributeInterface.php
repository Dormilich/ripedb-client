<?php
// AttributeInterface.php

namespace Dormilich\WebService\RIPE;

interface AttributeInterface
{
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
     * Convert attribute into a RIPE REST JSON compatible array.
     * 
     * @return array
     */
    public function toArray();

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