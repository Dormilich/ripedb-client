<?php
// Mntner.php

namespace Dormilich\WebService\RIPE;

use Dormilich\WebService\RIPE\AttributeInterface as Attr;

/**
 * A stand-in for objects that don’t have the appropriate class defined.
 */
class Dummy extends AbstractObject
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
            $this->create($this->getType(), Attr::REQUIRED, Attr::SINGLE);
        }
        $this->create($this->getPrimaryKeyName(), Attr::REQUIRED, Attr::SINGLE);

        // these attributes are common to all objects (as per documentation)
        $this->create('org',     Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('admin-c', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('tech-c',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('remarks', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('notify',  Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('mnt-by',  Attr::REQUIRED, Attr::SINGLE);
        $this->create('changed', Attr::OPTIONAL, Attr::MULTIPLE);
        $this->create('source',  Attr::REQUIRED, Attr::SINGLE);

        $this->generated('created');
        $this->generated('last-modified');
    }

    /**
     * Get an attribute. If it doesn’t exist, create it on-the-fly.
     * 
     * @param string $name Name of the candidate attribute.
     * @return AttributeInterface
     */
    public function getAttribute($name)
    {
        try {
            return parent::getAttribute($name);
        }
        catch (\Exception $exc) {
            return $this->setupAttribute($name);
        }
    }

    /**
     * Create an Attribute. If called implicit (e.g. via setAttribute()) it 
     * will create an optional, multiple attribute on-the-fly.
     * 
     * This method can be used to create an RPSL object according to a 
     * descriptor from the metadata service.
     * 
     * @param string $name name of the attribute.
     * @param boolean $required Requirement (mandatory) of the attribute.
     * @param boolean $multiple Cardinality (multiple) of the attribute.
     * @return AttributeInterface
     */
    public function setupAttribute($name, $required = Attr::OPTIONAL, $multiple = Attr::MULTIPLE)
    {
        $this->create($name, $required, $multiple);

        // to make sure we’re not caught in a loop
        return parent::getAttribute($name);
    }
}
