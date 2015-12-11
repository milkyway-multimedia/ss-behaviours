<?php namespace Milkyway\SS\Behaviours\Extensions;

/**
 * Milkyway Multimedia
 * Sluggable.php
 *
 * @package milkyway-multimedia/ss-behaviours
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use DataExtension;

use Milkyway\SS\Behaviours\Traits\Sluggable as CommonProperties;
use Milkyway\SS\Behaviours\Traits\SilverstripeExtension;

class Sluggable extends DataExtension
{
    use CommonProperties;
    use SilverstripeExtension;

    /**
     * Add a Sluggable extension to a DataObject
     *
     * DataObject:
     *   extensions:
     *     - Sluggable('ID','Salt',32,1)
     *
     * @param string $encryptUsing (must be of type Int)
     * @param string $salt
     * @param int $length
     * @param boolean $unique
     */
    public function __construct($encryptUsing = 'ID', $salt = '', $length = 32, $unique = true)
    {
        parent::__construct();
        $this->init($encryptUsing, $salt, $length, $unique);
        $this->initRecord();
    }

    public static function get_extra_config($class, $extensionClass, $args)
    {
        $length = isset($args[2]) ? $args[2] : 32;
        $dbField = isset($args[4]) ? $args[4] : 'Slug';
        $saltField = isset($args[5]) ? $args[5] : 'Slug_Salt';

        return [
            'db'      => [
                $dbField   => 'Varchar(' . $length . ')',
                $saltField => 'Varchar(255)',
            ],
            'indexes' => [
                $dbField => true,
            ],
        ];
    }

    /**
     * Hook for onBeforeWrite to generate a hash
     */
    public function onBeforeWrite()
    {
        if (!$this->executeOnBeforeWrite) {
            return;
        }

        $this->generateSlug();
    }

    /**
     * Hook for onAfterWrite to generate a hash
     */
    public function onAfterWrite()
    {
        if (!$this->executeOnAfterWrite) {
            return;
        }

        $this->generateSlugAndSave();
    }
}