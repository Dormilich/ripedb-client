<?php
// ObjectInterface.php

namespace Dormilich\WebService\RIPE;

interface ObjectInterface
{
    /**
     * Get the name of the current RIPE object.
     * 
     * @return string RIPE object name.
     */
    public function getType();

    /**
     * Get the value of the attribute defined as primary key.
     * 
     * @return string
     */
    public function getPrimaryKey();

    /**
     * Get the name of the PK via function. 
     * Conformance function to overwrite in the Dummy class, 
     * which can not use a constant to store the PK.
     * 
     * @return string
     */
    public function getPrimaryKeyName();

    /**
     * Get an attribute specified by name.
     * 
     * @param string $name Name of the attribute.
     * @return AttributeInterface Attribute object.
     * @throws InvalidAttributeException Invalid argument name.
     */
    public function getAttribute($name);

    /**
     * Check if any of the required Attributes is undefined.
     * 
     * @return boolean
     */
    public function isValid();

    /**
     * Convert object to a RIPE REST JSON compatible array.
     * 
     * @return array
     */
    public function toArray();

    /**
     * Convert object to a SimpleXML object.
     * 
     * @return SimpleXMLElement
     */
    public function toXML();
}
