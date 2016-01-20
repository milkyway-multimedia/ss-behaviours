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

    private function initRecord($workingRecordField = 'hashWorkingRecord') {
        $this->{$workingRecordField} = $this->owner;
    }

    public function setOwner($owner, $ownerBaseClass = null, $workingRecordField = 'hashWorkingRecord')
    {
        parent::setOwner($owner, $ownerBaseClass);
        $this->{$workingRecordField} = $this->owner;
    }
}
