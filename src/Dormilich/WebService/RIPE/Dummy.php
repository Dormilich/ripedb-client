<?php
// Mntner.php

namespace Dormilich\WebService\RIPE;

/**
 * A stand-in for objects that don’t have the appropriate class defined.
 */
class Dummy extends Object
{
    /**
     * Create stand-in object for any RIPE object data. 
     * It's the responsibility of the writer to figure out the correct values.
     * One notable difference to the regular objects is that it expects the 
     * Type and PK names (which may be the same), and not the Type’s value.
     * 
     * @param string $type The object type.
     * @param string $key The object’s primary key. If not set the Type is used.
     */
    public function __construct($type, $key = null)
    {
        $this->setType($type);

        if (null === $key) {
            $this->setKey($this->getType());
        } else {
            $this->setKey($key);
        }

        $this->init();
    }

    /**
     * Create an attribute for the primary key.
     * 
     * @return void
     */
    protected function init() 
    {
        if ($this->getType() !== $this->getPrimaryKeyName()) {
            // a type attribute (alternate lookup key) is usually required/single
            $this->create($this->getType(), Attribute::REQUIRED, Attribute::SINGLE);
        }
        $this->create($this->getPrimaryKeyName(), Attribute::REQUIRED, Attribute::SINGLE);

        // these attributes are common to all objects (as per documentation)
        $this->create('org',     Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('admin-c', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('tech-c',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('remarks', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('notify',  Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('mnt-by',  Attribute::REQUIRED, Attribute::SINGLE);
        $this->create('changed', Attribute::OPTIONAL, Attribute::MULTIPLE);
        $this->create('source',  Attribute::REQUIRED, Attribute::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }

    /**
     * Get an attribute. If it doesn’t exist, create it on-the-fly.
     * 
     * @param type $name 
     * @return type
     */
    protected function findAttribute($name)
    {
        try {
            return $this->getAttribute($name);
        }
        catch (\Exception $exc) {
            return $this->setupAttribute($name);
        }
    }

    /**
     * Set an attribute’s value(s). If an attribute does not exist yet, 
     * it is created with the optional and multiple flag beforehand. 
     * 
     * @param string $name Attribute name.
     * @param mixed $value Attibute value(s).
     * @return self
     */
    public function setAttribute($name, $value)
    {
        $this->findAttribute((string) $name)->setValue($value);

        return $this;
    }

    /**
     * Add a value to an attribute. If an attribute does not exist yet, 
     * it is created with the optional and multiple flag beforehand. 
     * 
     * @param string $name Attribute name.
     * @param mixed $value Attibute value(s).
     * @return self
     */
    public function addAttribute($name, $value)
    {
        $this->findAttribute((string) $name)->addValue($value);

        return $this;
    }

    /**
     * Create an Attribute. If called implicit (e.g. via setAttribute()) it 
     * will create an optional multiple attribute on-the-fly.
     * 
     * This method can be used to create a RIPE object according to a 
     * descriptor from the metadata service.
     * 
     * @param string $name name of the attribute.
     * @param boolean $required Requirement (mandatory) of the attribute.
     * @param boolean $multiple Cardinality (multiple) of the attribute.
     * @return Attribute
     */
    public function setupAttribute($name, $required = Attribute::OPTIONAL, $multiple = Attribute::MULTIPLE)
    {
        $this->create($name, $required, $multiple);

        return $this->attributes[$name];
    }
}
