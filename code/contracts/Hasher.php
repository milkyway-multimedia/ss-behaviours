<?php
namespace Milkyway\Hashable\Contracts;

/**
 * Milkyway Multimedia
 * Hasher.php
 *
 * @package milkyway-multimedia/hashable
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

interface Hasher {
    public function encrypt($value);
    public function decrypt($value);
}