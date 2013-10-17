<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 16.10.13
 * Time: 16:17
 * To change this template use File | Settings | File Templates.
 */

use DcaTools\Definition;

class SubPaletteTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Definition\SubPalette
	 */
	protected $objPalette;

	public function setUp()
	{
		$GLOBALS['TL_DCA']['tl_test'] = $GLOBALS['TEST_DCA'];
		$this->objPalette = Definition::getSubPalette('tl_test', 'sub');
	}

	public function tearDown()
	{
		$this->objPalette = null;
		$GLOBALS['TL_DCA']['tl_test'] = null;

		$ref = new ReflectionClass('DcaTools\Definition\DataContainer');
		$obj = $ref->newInstanceWithoutConstructor();

		$constructor = $ref->getConstructor();
		$constructor->setAccessible( true );

		$constructor->invokeArgs( $obj, array('tl_test') ) ;

		$refObject   = new ReflectionObject( $obj );
		$refProperty = $refObject->getProperty( 'arrDataContainers' );
		$refProperty->setAccessible( true );
		$refProperty->setValue(null, array());
	}

	public function testGetPropertyNames()
	{
		$this->assertEquals(array('test'), $this->objPalette->getPropertyNames());
	}

	public function testGetProperties()
	{
		$this->assertEquals(array('test' => $this->objPalette->getProperty('test')), $this->objPalette->getProperties());
	}

	public function testHasProperty()
	{
		$this->assertTrue($this->objPalette->hasProperty('test'));
		$this->assertFalse($this->objPalette->hasProperty('done'));
	}

	public function testGetProperty()
	{
		$this->assertInstanceOf('DcaTools\Definition\Property', $this->objPalette->getProperty('test'));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetPropertyException()
	{
		$this->objPalette->getProperty('done');
	}

	public function testAddProperty()
	{
		$objProperty = $this->objPalette->getDataContainer()->getProperty('done');

		$objProperty = $this->objPalette->addProperty($objProperty);
		$this->assertInstanceOf('DcaTools\Definition\Property', $objProperty);

		$this->assertTrue($this->objPalette->hasProperty('done'));
		$this->assertEquals(array_keys($GLOBALS['TL_DCA']['tl_test']['fields']), $this->objPalette->getPropertyNames());

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], $this->objPalette->asString());
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], 'test,done');
	}

	public function testRemoveProperty()
	{
		$this->assertEquals($this->objPalette, $this->objPalette->removeProperty('notexisting'));
		$this->assertEquals($this->objPalette, $this->objPalette->removeProperty('test'));

		$this->assertFalse($this->objPalette->hasProperty('test'));
		$this->assertTrue($this->objPalette->getDataContainer()->hasProperty('test'));

		$this->objPalette->removeProperty('test', true);
		$this->assertTrue($this->objPalette->getDataContainer()->hasProperty('test'));

		$this->objPalette->addProperty('test');
		$this->objPalette->removeProperty('test', true);
		$this->assertFalse($this->objPalette->getDataContainer()->hasProperty('test'));

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], '');
	}

	public function testMoveProperty()
	{
		$objProperty = $this->objPalette->getDataContainer()->getProperty('done');
		$this->objPalette->addProperty($objProperty, 'title');

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], 'test,done');

		$this->objPalette->moveProperty($objProperty, 'test', Definition::BEFORE);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], 'done,test');

		$this->objPalette->moveProperty($objProperty, 'test', Definition::AFTER);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], 'test,done');

		$this->objPalette->moveProperty($objProperty, 'test', Definition::FIRST);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], 'done,test');

		$this->objPalette->moveProperty($objProperty, 'test', Definition::LAST);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], 'test,done');

		$this->objPalette->moveProperty($objProperty, Definition::LAST);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], 'test,done');

		$this->objPalette->moveProperty($objProperty, Definition::LAST);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], 'test,done');
	}

	public function testCreateProperty()
	{
		$objProperty = $this->objPalette->createProperty('name');

		$this->assertInstanceOf('DcaTools\Definition\Property', $objProperty);
		$this->assertTrue($this->objPalette->getDataContainer()->hasProperty('name'));

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], 'test,name');

		$objProperty = $this->objPalette->createProperty('new');
		$this->assertInstanceOf('DcaTools\Definition\Property', $objProperty);
		$this->assertTrue($this->objPalette->getDataContainer()->hasProperty('new'));

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], 'test,name,new');
	}

	public function testHasSelectors()
	{
		$this->objPalette->addProperty('done');
		$this->assertTrue($this->objPalette->hasSelectors());

		$this->objPalette->removeProperty('done');
		$this->assertFalse($this->objPalette->hasSelectors());

		$this->objPalette->addProperty('done');
		$this->assertTrue($this->objPalette->hasSelectors());

		unset($GLOBALS['TL_DCA']['tl_test']['palettes']['__selector__']);
		$this->assertFalse($this->objPalette->hasSelectors());
	}

	public function testGetSelectors()
	{
		$this->assertEmpty($this->objPalette->getSelectors());

		$this->objPalette->addProperty('done');
		$this->assertArrayHasKey('done', $this->objPalette->getSelectors());
	}

	public function testExport()
	{
		$this->assertEquals('test', $this->objPalette->asString());
		$this->assertEquals(array('test'), $this->objPalette->asArray());
	}

	public function testRemove()
	{
		$objDc = $this->objPalette->getDataContainer();

		$this->assertEquals($this->objPalette, $this->objPalette->remove());

		$this->assertNull($this->objPalette->getDataContainer());
		$this->assertFalse($objDc->hasPalette('title'));

		$this->assertFalse(isset($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub']));
	}

	public function testExtend()
	{
		$objPalette = $this->objPalette->getDataContainer()->createSubPalette('second');
		$objPalette->extend($this->objPalette);

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], $GLOBALS['TL_DCA']['tl_test']['subpalettes']['second']);

		$objPalette->createProperty('final');
		$this->assertNotEquals($GLOBALS['TL_DCA']['tl_test']['subpalettes']['sub'], $GLOBALS['TL_DCA']['tl_test']['subpalettes']['second']);
	}
	
}