<?php

/**
 * Milkyway Multimedia
 * Hashable.php
 *
 * @package milkyway-multimedia/hashable
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */
class Hashable extends DataExtension {

    private static $db = array(
        'Hash'      => 'Varchar(32)',
    );

    private static $indexes = array(
        'Hash' => true,
    );

    // The field that stores the hash
    protected $dbField = 'Hash';

    // Length of hash
    protected $length = 32;

    // Create a hash before writing object
    protected $executeOnBeforeWrite = true;

    // Create a hash after writing an object (be careful of loops)
    protected $executeOnAfterWrite = false;

    // Hash must be unique on save
    protected $mustBeUnique = true;

    /**
     * Add a hashable extension/decorator to a DataObject
     *
     * DataObject:
     *   extensions:
     *     - Hashable(32, 1)
     *
     * @param int     $length
     * @param boolean $unique
     */
    public function __construct($length = 32, $unique = true)
    {
        parent::__construct();

        $this->length = $length;
        $this->mustBeUnique = (bool)$unique;
    }

    /**
     * Hook for onBeforeWrite to generate a hash
     */
    public function onBeforeWrite()
    {
        if (! $this->executeOnBeforeWrite)
        {
            return;
        }
        $this->generateHash();
    }

    /**
     * Hook for onAfterWrite to generate a hash
     */
    public function onAfterWrite()
    {
        if (! $this->executeOnAfterWrite)
        {
            return;
        }
        $this->generateHashAndSave();
    }

    /**
     * Generate a hash for this @DataObject
     */
    public function generateHash()
    {
        if (! $this->owner->{$this->dbField})
        {
            $this->owner->regenerateHash();
        }
    }

    /**
     * Regenerate a hash for this @DataObject
     */
    public function regenerateHash()
    {
        $this->owner->{$this->dbField} = $this->encrypt();

        if($this->mustBeUnique && !$this->owner->hasUniqueHash())
            $this->regenerateHash();
    }

    /**
     * Generate hash and save if hash created
     */
    protected function generateHashAndSave()
    {
        if (! $this->owner->{$this->dbField})
        {
            $this->owner->generateHash();

            if ($this->owner->{$this->dbField})
                $this->owner->write();
        }
    }

    /**
     * Find a @DataObject by hash
     *
     * @param string $hash
     *
     * @return DataObject|null
     */
    public function findByHash($hash)
    {
        return $this->owner->get()->filter($this->dbField, $hash)->first();
    }

    /**
     * Check if the hash for this object is unique
     *
     * @return boolean
     */
    public function hasUniqueHash()
    {
        $dbField = $this->dbField;
        $hash      = $this->owner->$dbField ? : $this->owner->encrypt();

        return ! ($this->owner->get()->filter($dbField, $hash)->exclude('ID', $this->owner->ID)->exists());
    }

    protected function encrypt()
    {
        $generator = new RandomGenerator();
        return substr($generator->randomToken(), 0, $this->length);
    }
}