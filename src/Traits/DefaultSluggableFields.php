<?php namespace Milkyway\SS\Behaviours\Traits;

/**
 * Milkyway Multimedia
 * DefaultSluggableFields.php
 *
 * @package milkyway-multimedia/ss-behaviours
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

trait DefaultSluggableFields
{
    private static $db = [
        'Slug' => 'Varchar(32)',
        'Slug_Salt' => 'Varchar(256)',
    ];

    private static $indexes = [
        'Slug' => true,
    ];
}
