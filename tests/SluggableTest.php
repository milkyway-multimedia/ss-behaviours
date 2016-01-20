<?php namespace Milkyway\SS\Behaviours\Tests;

/**
 * Milkyway Multimedia
 * HashableTest.php
 *
 * @package milkyway-multimedia/ss-hashable
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

use DataObject;
use Milkyway\SS\Behaviours\Traits\DefaultSluggableFields;
use Milkyway\SS\Behaviours\Traits\Sluggable;

class SluggableTest extends \SapphireTest
{
    protected $requiredExtensions = [
        'Milkyway\\SS\\Behaviours\\Tests\\SluggableTest_Object' => [
            'Milkyway\\SS\\Behaviours\\Extensions\\Sluggable',
        ],
    ];

    protected $extraDataObjects = [
        'Milkyway\\SS\\Behaviours\\Tests\\SluggableTest_Object',
        'Milkyway\\SS\\Behaviours\\Tests\\SluggableTest_WithTrait',
    ];

    public function testExtension()
    {
        $object = new SluggableTest_Object;
        $object->write();
        $object->regenerateSlug();
        $this->assertNotEquals($object->ID, $object->Slug);
        $this->assertEquals($object->ID, $object->decrypt());
        $this->assertNotNull($object->Slug);
    }

    public function testTrait()
    {
        $object = new SluggableTest_WithTrait;
        $object->write();
        $object->regenerateSlug();
        $this->assertNotEquals($object->ID, $object->Slug);
        $this->assertEquals($object->ID, $object->decrypt());
        $this->assertNotNull($object->Slug);
    }
}

class SluggableTest_Object extends DataObject implements \TestOnly
{
    private static $extensions = [
        "Milkyway\\SS\\Behaviours\\Extensions\\Sluggable",
    ];
}

class SluggableTest_WithTrait extends DataObject implements \TestOnly
{
    use Sluggable;
    use DefaultSluggableFields;

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }
}
