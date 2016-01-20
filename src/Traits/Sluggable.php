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
    protected $slugHasher;

    // The field to hash
    protected $slugDbField = 'Slug';

    // The salt that was used to generate this hash
    protected $slugDbFieldForSalt = 'Slug_Salt';

    // The field to encrypt
    protected $slugEncryptUsing = 'ID';

    // The default encryption salt (best to change this)
    protected $slugSalt = 'Hmm.... Salty';

    // Length of hash
    protected $slugLength = 32;

    // Slug must be unique on save
    protected $slugMustBeUnique = true;

    private $slugWorkingRecord;

    public function init(
        $encryptUsing = 'ID',
        $salt = '',
        $length = 32,
        $unique = true,
        $dbField = '',
        $dbFieldForSalt = ''
    ) {
        $this->slugEncryptUsing = $encryptUsing;

        if ($salt) {
            $this->slugSalt = $salt;
        }

        $this->slugLength = $length;
        $this->mustBeUnique = (bool)$unique;

        if ($dbField) {
            $this->slugDbField = $dbField;
        }

        if ($dbFieldForSalt) {
            $this->slugDbFieldForSalt = $dbFieldForSalt;
        }

        $this->slugWorkingRecord = $this;
    }

    /**
     * Generate a slug for this @DataObject
     */
    public function generateSlug()
    {
        if ($this->slugWorkingRecord->{$this->slugDbField}) {
            return;
        }

        $this->regenerateSlug();
    }

    /**
     * Generate hash and save if slug created
     */
    protected function generateSlugAndSave()
    {
        if ($this->slugWorkingRecord->{$this->slugDbField}) {
            return;
        }

        $this->generateSlug();

        if ($this->slugWorkingRecord->{$this->slugDbField}) {
            $this->slugWorkingRecord->write();
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
        return $this->slugWorkingRecord->get()->filter($this->slugDbField, $slug)->first();
    }

    /**
     * Regenerate a slug for this @DataObject
     */
    public function regenerateSlug()
    {
        $this->slugWorkingRecord->{$this->slugDbField} = $this->encrypt();
        $salt = $this->slugSalt;

        if ($this->mustBeUnique && !$this->hasUniqueSlug()) {
            $generator = new RandomGenerator();

            while (!$this->hasUniqueSlug()) {
                $salt = $generator->randomToken();
                $this->slugWorkingRecord->{$this->slugDbField} = $this->encrypt(null, $salt);
            }
        }

        $this->slugWorkingRecord->{$this->slugDbFieldForSalt} = $salt;
    }

    /**
     * Check if the slug for this object is unique
     *
     * @return boolean
     */
    public function hasUniqueSlug()
    {
        $hash = $this->slugWorkingRecord->{$this->slugDbField} ?: $this->encrypt();
        $list = $this->slugWorkingRecord->get()->filter($this->slugDbField, $hash);

        if($this->slugWorkingRecord->ID) {
            $list = $list->exclude('ID', $this->slugWorkingRecord->ID);
        }
        else {
            $list = $list->exclude('ID', 0);
        }

        return !($list->exists());
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
        $return = $this->hasher($salt)->decode($value ?: $this->slugWorkingRecord->{$this->slugDbField});

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
            if (is_array($this->slugEncryptUsing)) {
                foreach ($this->slugEncryptUsing as $field) {
                    $encryptUsing .= $this->slugWorkingRecord->$field;
                }
            } else {
                $encryptUsing = $this->slugWorkingRecord->{$this->slugEncryptUsing};
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
        $salt = $salt ?: $this->slugWorkingRecord->{$this->slugDbFieldForSalt} ?: $this->slugSalt;

        if ($salt) {
            return Object::create('Milkyway\SS\Behaviours\Contracts\Slugger', $salt, $this->slugLength);
        }

        if (!$this->slugHasher) {
            $this->slugHasher = Object::create('Milkyway\SS\Behaviours\Contracts\Slugger', $salt, $this->slugLength);
        }

        return $this->slugHasher;
    }
}
