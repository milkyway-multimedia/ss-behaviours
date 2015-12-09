<?php namespace Milkyway\SS\Behaviours\Extensions;

/**
 * Milkyway Multimedia
 * Hashable.php
 *
 * @package milkyway-multimedia/ss-behaviours
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use DataExtension;

use \Milkyway\SS\Behaviours\Traits\Hashable as CommonProperties;
use Milkyway\SS\Behaviours\Traits\SilverstripeExtension;

class Hashable extends DataExtension
{
    use CommonProperties;
    use SilverstripeExtension;

    /**
     * Add a hashable extension/decorator to a DataObject
     *
     * DataObject:
     *   extensions:
     *     - Hashable(32, 1)
     *
     * @param int $length
     * @param boolean $unique
     * @param string $dbField
     */
    public function __construct($length = 32, $unique = true, $dbField = 'Hash')
    {
        parent::__construct();
        $this->init($length, $unique, $dbField);
        $this->initRecord();
    }

    public static function get_extra_config($class, $extensionClass, $args)
    {
        $length = isset($args[0]) ? $args[0] : 32;
        $dbField = isset($args[2]) ? $args[2] : 'Hash';

        return [
            'db' => [
                $dbField => 'Varchar(' . $length . ')',
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

        $this->generateHash();
    }

    /**
     * Hook for onAfterWrite to generate a hash
     */
    public function onAfterWrite()
    {
        if (!$this->executeOnAfterWrite) {
            return;
        }

        $this->generateHashAndSave();
    }
}