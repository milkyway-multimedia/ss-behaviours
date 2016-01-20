<?php namespace Milkyway\SS\Behaviours\Tests;

/**
 * Milkyway Multimedia
 * HashableTest.php
 *
 * @package milkyway-multimedia/ss-hashable
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use DataObject;
use Milkyway\SS\Behaviours\Traits\Hashable;

class HashableTest extends \SapphireTest
{
    protected $usesDatabase = true;

    protected $requiredExtensions = [
        'Milkyway\\SS\\Behaviours\\Tests\\HashableTest_Object' => [
            'Milkyway\\SS\\Behaviours\\Extensions\\Hashable',
        ],
    ];

    protected $extraDataObjects = [
        'Milkyway\\SS\\Behaviours\\Tests\\HashableTest_Object',
        'Milkyway\\SS\\Behaviours\\Tests\\HashableTest_WithTrait',
    ];

    public function testExtension()
    {
        $object = new HashableTest_Object;
        $object->Title = 'test';
        $object->regenerateHash();
        $this->assertNotNull($object->Hash);
        $this->assertNotEquals($object->ID, $object->Hash);
    }

    public function testTrait()
    {
        $object = new HashableTest_WithTrait;
        $object->write();
        $object->regenerateHash();
        $this->assertNotNull($object->Hash);
        $this->assertNotEquals($object->ID, $object->Hash);
    }
}

class HashableTest_Object extends DataObject implements \TestOnly
{
    private static $db = [
        'Title' => 'Varchar',
    ];

    private static $extensions = [
        'Milkyway\\SS\\Behaviours\\Extensions\\Hashable',
    ];
}

class HashableTest_WithTrait extends DataObject implements \TestOnly
{
    use Hashable;

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }
}
