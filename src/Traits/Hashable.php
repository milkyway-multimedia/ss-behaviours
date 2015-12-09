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
    protected $dbField = 'Hash';

    // Length of hash
    protected $length = 32;

    // Hash must be unique on save
    protected $mustBeUnique = true;

    private $workingRecord;

    public function init($length = 32, $unique = true, $dbField = '')
    {
        $this->length = $length;
        $this->mustBeUnique = (bool)$unique;

        if($dbField) {
            $this->dbField = $dbField;
        }

        $this->workingRecord = $this;
    }

    /**
     * Generate a hash for this @DataObject
     */
    public function generateHash()
    {
        if (!$this->workingRecord->{$this->dbField}) {
            return;
        }

        $this->regenerateHash();
    }

    /**
     * Regenerate a hash for this @DataObject
     */
    public function regenerateHash()
    {
        $this->workingRecord->{$this->dbField} = $this->encrypt();

        if ($this->mustBeUnique && !$this->hasUniqueHash()) {
            $this->regenerateHash();
        }
    }

    /**
     * Generate hash and save if hash created
     */
    protected function generateHashAndSave()
    {
        if (!$this->workingRecord->{$this->dbField}) {
            $this->generateHash();

            if ($this->workingRecord->{$this->dbField}) {
                $this->workingRecord->write();
            }
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
        return $this->workingRecord->get()->filter($this->dbField, $hash)->first();
    }

    /**
     * Check if the hash for this object is unique
     *
     * @return boolean
     */
    public function hasUniqueHash()
    {
        $hash = $this->workingRecord->{$this->dbField} ?: $this->encrypt();

        return !($this->workingRecord->get()->filter($this->dbField, $hash)->exclude('ID', $this->workingRecord->ID)->exists());
    }

    protected function encrypt()
    {
        return substr((new RandomGenerator)->randomToken(), 0, $this->length);
    }
}