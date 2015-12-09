<?php namespace Milkyway\SS\Behaviours\Traits;

/**
 * Milkyway Multimedia
 * SetOwner.php
 *
 * @package milkyway-multimedia/ss-behaviours
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

trait SilverstripeExtension
{
    // Create a hash before writing object
    protected $executeOnBeforeWrite = true;

    // Create a hash after writing an object (be careful of loops)
    protected $executeOnAfterWrite = false;

    private function initRecord() {
        $this->workingRecord = $this->owner;
    }

    public function setOwner($owner, $ownerBaseClass = null)
    {
        parent::setOwner($owner, $ownerBaseClass);
        $this->workingRecord = $this->owner;
    }
}