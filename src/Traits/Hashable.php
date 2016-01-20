<?php namespace Milkyway\SS\Behaviours\Traits;

/**
 * Milkyway Multimedia
 * Hashable.php
 *
 * @package milkyway-multimedia/ss-behaviours
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use RandomGenerator;

trait Hashable
{
    // The field that stores the hash
    protected $hashDbField = 'Hash';

    // Length of hash
    protected $hashLength = 32;

    // Hash must be unique on save
    protected $hashMustBeUnique = true;

    private $hashWorkingRecord;

    public function init($length = 32, $unique = true, $dbField = '')
    {
        $this->hashLength = $length;
        $this->hashMustBeUnique = (bool)$unique;

        if($dbField) {
            $this->hashDbField = $dbField;
        }

        $this->hashWorkingRecord = $this;
    }

    /**
     * Generate a hash for this @DataObject
     */
    public function generateHash()
    {
        if ($this->hashWorkingRecord->{$this->hashDbField}) {
            return;
        }

        $this->regenerateHash();
    }

    /**
     * Regenerate a hash for this @DataObject
     */
    public function regenerateHash()
    {
        $this->hashWorkingRecord->{$this->hashDbField} = $this->encrypt();

        if ($this->hashMustBeUnique && !$this->hasUniqueHash()) {
            $this->regenerateHash();
        }
    }

    /**
     * Generate hash and save if hash created
     */
    protected function generateHashAndSave()
    {
        if ($this->hashWorkingRecord->{$this->hashDbField}) {
            return;
        }

        $this->generateHash();

        if ($this->hashWorkingRecord->{$this->hashDbField}) {
            $this->hashWorkingRecord->write();
        }
    }

    /**
     * Find a @DataObject by hash
     *
     * @param string $hash
     *
     * @return \DataObject|null
     */
    public function findByHash($hash)
    {
        return $this->hashWorkingRecord->get()->filter($this->hashDbField, $hash)->first();
    }

    /**
     * Check if the hash for this object is unique
     *
     * @return boolean
     */
    public function hasUniqueHash()
    {
        $hash = $this->hashWorkingRecord->{$this->hashDbField} ?: $this->encrypt();

        $list = $this->hashWorkingRecord->get()->filter($this->hashDbField, $hash);

        if($this->hashWorkingRecord->ID) {
            $list = $list->exclude('ID', $this->hashWorkingRecord->ID);
        }

        return !($list->exists());
    }

    protected function encrypt()
    {
        return substr((new RandomGenerator)->randomToken(), 0, $this->hashLength);
    }
}
