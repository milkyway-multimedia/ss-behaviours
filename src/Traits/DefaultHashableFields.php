<?php namespace Milkyway\SS\Behaviours\Traits;

/**
 * Milkyway Multimedia
 * DefaultHashableFields.php
 *
 * @package milkyway-multimedia/ss-behaviours
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

trait DefaultHashableFields
{
    private static $db = [
        'Hash' => 'Varchar(32)',
    ];

    private static $indexes = [
        'Hash' => true,
    ];
}
