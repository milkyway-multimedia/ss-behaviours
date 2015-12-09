<?php namespace Milkyway\SS\Behaviours\Traits;

/**
 * Milkyway Multimedia
 * Sluggable.php
 *
 * @package milkyway-multimedia/ss-behaviours
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use Milkyway\SS\Behaviours\Contracts\Slugger;
use RandomGenerator;
use Object;

trait Sluggable
{
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

    // Slug must be unique on save
    protected $mustBeUnique = true;

    private $workingRecord;

    public function init(
        $encryptUsing = 'ID',
        $salt = '',
        $length = 32,
        $unique = true,
        $dbField = '',
        $dbFieldForSalt = ''
    ) {
        $this->encryptUsing = $encryptUsing;

        if ($salt) {
            $this->salt = $salt;
        }

        $this->length = $length;
        $this->mustBeUnique = (bool)$unique;

        if ($dbField) {
            $this->dbField = $dbField;
        }

        if ($dbFieldForSalt) {
            $this->dbFieldForSalt = $dbFieldForSalt;
        }

        $this->workingRecord = $this;
    }

    /**
     * Generate a hash for this @DataObject
     */
    public function generateSlug()
    {
        if (!$this->workingRecord->{$this->dbField}) {
            return;
        }

        $this->regenerateSlug();
    }

    /**
     * Generate hash and save if slug created
     */
    protected function generateSlugAndSave()
    {
        if (!$this->workingRecord->{$this->dbField}) {
            $this->generateSlug();

            if ($this->workingRecord->{$this->dbField}) {
                $this->workingRecord->write();
            }
        }
    }

    /**
     * Find a @DataObject by slug
     *
     * @param string $slug
     *
     * @return \DataObject|null
     */
    public function findBySlug($slug)
    {
        return $this->workingRecord->get()->filter($this->dbField, $slug)->first();
    }

    /**
     * Regenerate a slug for this @DataObject
     */
    public function regenerateSlug()
    {
        $this->workingRecord->{$this->dbField} = $this->encrypt();
        $salt = $this->salt;

        if ($this->mustBeUnique && !$this->hasUniqueSlug()) {
            $generator = new RandomGenerator();

            while (!$this->hasUniqueSlug()) {
                $salt = $generator->randomToken();
                $this->workingRecord->{$this->dbField} = $this->encrypt($salt);
            }
        }

        $this->workingRecord->{$this->dbFieldForSalt} = $salt;
    }

    /**
     * Check if the slug for this object is unique
     *
     * @return boolean
     */
    public function hasUniqueSlug()
    {
        $hash = $this->workingRecord->{$this->dbField} ?: $this->encrypt();

        return !($this->workingRecord->get()->filter($this->dbField, $hash)->exclude('ID', $this->workingRecord->ID)->exists());
    }

    /**
     * Encrypt the value
     *
     * @param string|int $value
     * @param string $salt
     *
     * @return string
     */
    protected function encrypt($value = null, $salt = '')
    {
        return $this->hasher($salt)->encode($this->findValueToSlug($value));
    }

    /**
     * Decrypt value
     *
     * @param string|int $value
     * @param string $salt
     *
     * @return array
     */
    public function decrypt($value = null, $salt = '')
    {
        $return = $this->hasher($salt)->decode($value ?: $this->workingRecord->{$this->dbField});

        if(is_array($return)) {
            $return = array_pop($return);
        }

        return $return;
    }

    /**
     * Return value to hash
     *
     * @param array|string $encryptUsing
     *
     * @return int|string
     */
    protected function findValueToSlug($encryptUsing = null)
    {
        if (!$encryptUsing) {
            if (is_array($this->encryptUsing)) {
                foreach ($this->encryptUsing as $field) {
                    $encryptUsing .= $this->workingRecord->$field;
                }
            } else {
                $encryptUsing = $this->workingRecord->{$this->encryptUsing};
            }
        }

        return is_numeric($encryptUsing) ? $encryptUsing : implode(array_map(function ($n) {
            return sprintf('%03d', $n);
        }, unpack('C*', $encryptUsing)));
    }

    /**
     * Return the hasher service
     *
     * @param string $salt
     *
     * @return Slugger
     */
    protected function hasher($salt = '')
    {
        $salt = $salt ?: $this->workingRecord->{$this->dbFieldForSalt};

        if ($salt) {
            return Object::create('Milkyway\SS\Behaviours\Contracts\Slugger', $salt, $this->length);
        }

        if (!$this->hasher) {
            $this->hasher = Object::create('Milkyway\SS\Behaviours\Contracts\Slugger', $this->salt, $this->length);
        }

        return $this->hasher;
    }
}