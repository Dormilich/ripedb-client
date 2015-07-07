<?php
// Mntner.php

namespace Dormilich\WebService\RIPE;

/**
 * A stand-in for objects that don’t have the appropriate class defined.
 */
class Dummy extends Object
{
    /**
     * In case someone uses a function that requires this constant.
     */
    const PRIMARYKEY = 0;

    private $primaryKey;

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
        $this->type = (string) $type;

        if (null === $key) {
            $this->primaryKey = $this->type;
        } else {
            $this->primaryKey = (string) $key;
        }

        $this->init();
    }

    /**
     * Create an attribute for the primary key.
     * 
     * @return void
     */
    public function init() 
    {
        if ($this->type !== $this->primaryKey) {
            // a type attribute (alternate lookup key) is usually required/single
            $this->create($this->type, Attribute::REQUIRED, Attribute::SINGLE);
        }
        $this->create($this->primaryKey, Attribute::REQUIRED, Attribute::SINGLE);

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
     * Override, since the PK is not known in advance the PK property is used.
     * 
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->getAttribute($this->primaryKey)->getValue();
    }

    /**
     * Get the name of the primary key.
     * 
     * @return sring PK name.
     */
    public function getPrimaryKeyName()
    {
        return $this->primaryKey;
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
        $name = (string) $name;
        $this->setupAttribute($name)->setValue($value);

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
        $name = (string) $name;
        $this->setupAttribute($name)->addValue($value);

        return $this;
    }

    /**
     * Create an Attribute if necessary.
     * 
     * @param string $name Attribute name.
     * @return void
     */
    private function setupAttribute($name)
    {
        if (!isset($this->attributes[$name])) {
            $this->create($name, Attribute::OPTIONAL, Attribute::MULTIPLE);
        }
        return $this->attributes[$name];
    }
}
