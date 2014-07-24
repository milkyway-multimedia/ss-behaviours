<?php
namespace Milkyway\SS\Hashable\Contracts;

/**
 * Milkyway Multimedia
 * Slugger.php
 *
 * @package milkyway-multimedia/hashable
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

interface Slugger {
    public function encrypt($value);
    public function decrypt($value);
}