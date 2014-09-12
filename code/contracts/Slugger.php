<?php
namespace Milkyway\SS\Hashable\Contracts;

/**
 * Milkyway Multimedia
 * Slugger.php
 *
 * @package milkyway-multimedia/ss-hashable
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

interface Slugger {
    public function encode($value);
    public function decode($value);
}