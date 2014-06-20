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
        'Hash_Salt' => 'Varchar(256)',
    );

    private static $indexes = array(
        'Hash' => true,
    );

    // The Hash Service object for this Extension
    protected $hasher;

    // The field to hash
    protected $dbField = 'Hash';

    // The salt that was used to generate this hash
    protected $dbFieldForSalt = 'Hash_Salt';

    // The field to encrypt
    protected $encryptUsing = 'ID';

    // The encryption salt (best to change this)
    protected $salt = 'Hmm.... Salty';

    // Length of hash
    protected $length = 32;

    // Create a hash before writing object
    protected $onBeforeWrite = false;

    // Create a hash after writing an object (be careful of loops)
    protected $onAfterWrite = true;

    // Hash must be unique on save
    protected $mustBeUnique = true;

    /**
     * Add a hashable extension/decorator to a DataObject
     *
     * DataObject:
     *   extensions:
     *     - Hashable('ID','Salt',32)
     *
     * @param string  $encryptUsing
     * @param string  $salt
     * @param int     $length
     * @param boolean $unique
     */
    public function __construct($encryptUsing = 'ID', $salt = '', $length = 32, $unique = true)
    {
        parent::__construct();
        $this->encryptUsing = $encryptUsing;
        if ($salt)
        {
            $this->salt = $salt;
        }
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
        $this->generateHashAndSave();
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
        $dbField = $this->dbField;

        if (! $this->owner->$dbField)
        {
            $this->owner->$dbField = $this->encrypt();
            $salt = $this->salt;

            if($this->mustBeUnique && !$this->owner->hasUniqueHash()) {
                $generator = new RandomGenerator();

                while(!$this->owner->hasUniqueHash()) {
                    $salt = $generator->randomToken();
                    $this->owner->$dbField = $this->encrypt($salt);
                }
            }

            $this->owner->{$this->dbFieldForSalt} = $salt;
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

    /**
     * Generate hash and save if hash created
     */
    protected function generateHashAndSave()
    {
        $this->owner->generateHash();
        if (! $this->owner->{$this->dbField})
        {
            $this->owner->write();
        }
    }

    /**
     * Encrypt value
     *
     * @param int|string $value
     *
     * @return string
     */
    protected function encrypt($value = null, $salt = '')
    {
        return $this->hasher($salt)->encrypt($this->findValueToHash($value));
    }

    /**
     * Decrypt value
     *
     * @param int|string $value
     *
     * @return string
     */
    protected function decrypt($value = null, $salt = '')
    {
        return $this->hasher($salt)->decrypt($value ? : $this->owner->{$this->hashField});
    }

    /**
     * Return value to hash
     *
     * @param array|string $encryptUsing
     *
     * @return int|string
     */
    protected function findValueToHash($encryptUsing = null)
    {
        if (! $encryptUsing)
        {
            if (is_array($this->encryptUsing))
            {
                foreach ($this->encryptUsing as $field)
                {
                    $encryptUsing .= $this->owner->$field;
                }
            } else
            {
                $encryptUsing = $this->owner->{$this->encryptUsing};
            }
        }

        return is_numeric($encryptUsing) ? $encryptUsing : implode(array_map(function ($n) { return sprintf('%03d', $n); }, unpack('C*', $encryptUsing)));
    }

    /**
     * Return the hasher service
     *
     * @todo Should be injected no?
     *
     * @param string $salt
     *
     * @return Hashids\Hashids|Milkyway\Hashable\Contracts\Hasher
     */
    protected function hasher($salt = '')
    {
        $salt = $salt ?: $this->owner->{$this->dbFieldForSalt};

        if($salt)
            return new Hashids\Hashids($salt, $this->length);

        if (! $this->hasher)
        {
            $this->hasher = new Hashids\Hashids($this->salt, $this->length);
        }

        return $this->hasher;
    }
}