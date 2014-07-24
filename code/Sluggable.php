<?php

/**
 * Milkyway Multimedia
 * Sluggable.php
 *
 * @package milkyway-multimedia/hashable
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */
class Sluggable extends Hashable {

    private static $db = array(
        'Slug'      => 'Varchar(32)',
        'Slug_Salt' => 'Varchar(256)',
    );

    private static $indexes = array(
        'Slug' => true,
    );

    // The Hash Service object for this Extension
    protected $hasher;

    // The field to hash
    protected $dbField = 'Slug';

    // The salt that was used to generate this hash
    protected $dbFieldForSalt = 'Slug_Salt';

    // The field to encrypt
    protected $encryptUsing = 'ID';

    // The encryption salt (best to change this)
    protected $salt = 'Hmm.... Salty';

    // Length of hash
    protected $length = 32;

    // Create a hash before writing object
    protected $executeOnBeforeWrite = false;

    // Create a hash after writing an object (be careful of loops)
    protected $executeOnAfterWrite = true;

    // Hash must be unique on save
    protected $mustBeUnique = true;

    /**
     * Add a hashable extension/decorator to a DataObject
     *
     * DataObject:
     *   extensions:
     *     - Sluggable('ID','Salt',32,1)
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
     * Regenerate a hash for this @DataObject
     */
    public function regenerateHash()
    {
        $this->owner->{$this->dbField} = $this->encrypt();
        $salt = $this->salt;

        if($this->mustBeUnique && !$this->owner->hasUniqueHash()) {
            $generator = new RandomGenerator();

            while(!$this->owner->hasUniqueHash()) {
                $salt = $generator->randomToken();
                $this->owner->{$this->dbField} = $this->encrypt($salt);
            }
        }

        $this->owner->{$this->dbFieldForSalt} = $salt;
    }

    /**
     * Encrypt the value
     *
     * @param string|int  $value
     * @param string $salt
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
     * @param string|int   $value
     * @param string $salt
     *
     * @return array
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
     * @return Hashids\Hashids|Milkyway\SS\Hashable\Contracts\Hasher
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