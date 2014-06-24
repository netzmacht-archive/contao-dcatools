<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 16.10.13
 * Time: 16:17
 * To change this template use File | Settings | File Templates.
 */

use deprecated\DcaTools\Definition;

class PaletteTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Definition\Palette
	 */
	protected $objPalette;

	public function setUp()
	{
		$GLOBALS['TL_DCA']['tl_test'] = $GLOBALS['TEST_DCA'];
		$this->objPalette = Definition::getPalette('tl_test');
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

		$this->assertTrue($this->objPalette->hasLegend('default'));

		$this->assertTrue($this->objPalette->hasProperty('done'));
		$this->assertEquals(array_keys($GLOBALS['TL_DCA']['tl_test']['fields']), $this->objPalette->getPropertyNames());

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], $this->objPalette->asString());
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test;{default_legend},done');
	}

	public function testAddProperty2()
	{
		$objProperty = $this->objPalette->getDataContainer()->getProperty('done');

		$objProperty = $this->objPalette->addProperty($objProperty, 'title');
		$this->assertInstanceOf('DcaTools\Definition\Property', $objProperty);

		$this->assertFalse($this->objPalette->hasLegend('default'));

		$this->assertTrue($this->objPalette->hasProperty('done'));
		$this->assertEquals(array_keys($GLOBALS['TL_DCA']['tl_test']['fields']), $this->objPalette->getPropertyNames());

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], $this->objPalette->asString());
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test,done');
	}

	public function testRemoveProperty()
	{
		$this->assertEquals($this->objPalette, $this->objPalette->removeProperty('notexisting'));
		$this->assertEquals($this->objPalette, $this->objPalette->removeProperty('test'));

		$this->assertFalse($this->objPalette->hasProperty('test'));
		$this->assertTrue($this->objPalette->getDataContainer()->hasProperty('test'));

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '');
	}

	public function testMoveProperty()
	{
		$objProperty = $this->objPalette->getDataContainer()->getProperty('done');
		$this->objPalette->addProperty($objProperty, 'title');

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test,done');

		$this->objPalette->moveProperty($objProperty, 'title', 'test', Definition::BEFORE);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},done,test');

		$this->objPalette->moveProperty($objProperty, 'title', 'test', Definition::AFTER);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test,done');

		$this->objPalette->moveProperty($objProperty, 'title', Definition::FIRST);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},done,test');

		$this->objPalette->moveProperty($objProperty, 'title', Definition::LAST);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test,done');

		$this->objPalette->moveProperty($objProperty, 'test', Definition::LAST);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test;{test_legend},done');

		$this->objPalette->moveProperty($objProperty, 'test', Definition::LAST);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test;{test_legend},done');
	}

	public function testCreateProperty()
	{
		$objProperty = $this->objPalette->createProperty('name', 'title');

		$this->assertInstanceOf('DcaTools\Definition\Property', $objProperty);
		$this->assertTrue($this->objPalette->getDataContainer()->hasProperty('name'));

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test,name');

		$objProperty = $this->objPalette->createProperty('new');
		$this->assertInstanceOf('DcaTools\Definition\Property', $objProperty);
		$this->assertTrue($this->objPalette->getDataContainer()->hasProperty('new'));

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test,name;{default_legend},new');
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

	public function testGetLegend()
	{
		$this->assertInstanceOf('DcaTools\Definition\Legend', $this->objPalette->getLegend('title'));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetLegendException()
	{
		$this->objPalette->getLegend('done');
	}

	public function testHasLegend()
	{
		$this->assertTrue($this->objPalette->hasLegend('title'));
		$this->assertFalse($this->objPalette->hasLegend('done'));
	}

	public function testCreateLegend()
	{
		$objLegend = $this->objPalette->createLegend('new');
		$this->assertInstanceOf('DcaTools\Definition\Legend', $objLegend);

		$objLegend->addProperty('done');
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test;{new_legend},done');
	}

	public function testMoveLegend()
	{
		$objLegend = $this->objPalette->createLegend('new');
		$objLegend->addProperty('done');

		$this->assertEquals($this->objPalette, $this->objPalette->moveLegend($objLegend, 'title', Definition::BEFORE));
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{new_legend},done;{title_legend},test');

		$this->assertEquals($this->objPalette,  $this->objPalette->moveLegend($objLegend, 'title', Definition::AFTER));
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test;{new_legend},done');

		$this->assertEquals($this->objPalette, $this->objPalette->moveLegend($objLegend, 'title', Definition::FIRST));
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{new_legend},done;{title_legend},test');

		$this->assertEquals($this->objPalette,  $this->objPalette->moveLegend($objLegend, 'title', Definition::LAST));
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test;{new_legend},done');

		$this->assertEquals($this->objPalette, $this->objPalette->moveLegend($objLegend, Definition::FIRST));
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{new_legend},done;{title_legend},test');

		$this->assertEquals($this->objPalette,  $this->objPalette->moveLegend($objLegend, Definition::LAST));
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{title_legend},test;{new_legend},done');
	}

	public function testRemoveLegend()
	{
		$this->assertEquals($this->objPalette, $this->objPalette->removeLegend('title'));
		$this->assertFalse($this->objPalette->hasLegend('title'));
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '');

		$objLegend = $this->objPalette->createLegend('title');
		$objLegend->addProperty('test');

		$this->assertTrue($this->objPalette->hasLegend('title'));
		$this->assertNotEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '');

		$this->objPalette->removeLegend($objLegend);
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '');
	}

	public function testExport()
	{
		$this->assertEquals('{title_legend},test', $this->objPalette->asString());
		$this->assertEquals(array('title' => array('test')), $this->objPalette->asArray());

		$this->objPalette->getLegend('title')->addModifier('hide');

		$this->assertEquals('{title_legend:hide},test', $this->objPalette->asString());
		$this->assertEquals(array('title' => array('test')), $this->objPalette->asArray());
		$this->assertEquals(array('title' => array(':hide', 'test')), $this->objPalette->asArray(true));
	}

	public function testRemove()
	{
		$objDc = $this->objPalette->getDataContainer();

		$this->assertEquals($this->objPalette, $this->objPalette->remove());

		$this->assertNull($this->objPalette->getDataContainer());
		$this->assertFalse($objDc->hasPalette('title'));

		$this->assertFalse(isset($GLOBALS['TL_DCA']['tl_test']['palettes']['title']));
	}

	public function testExtend()
	{
		$objPalette = $this->objPalette->getDataContainer()->createPalette('second');
		$objPalette->extend($this->objPalette);

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], $GLOBALS['TL_DCA']['tl_test']['palettes']['second']);

		$objPalette->createProperty('final');
		$this->assertNotEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], $GLOBALS['TL_DCA']['tl_test']['palettes']['second']);
	}

}