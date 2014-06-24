<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 11.10.13
 * Time: 13:19
 * To change this template use File | Settings | File Templates.
 */

use deprecated\DcaTools\Definition;

class PropertyTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Definition\Property
	 */
	protected $objProperty;

	public function setUp()
	{
		$GLOBALS['TL_DCA']['tl_test'] = $GLOBALS['TEST_DCA'];
		$this->objProperty = Definition::getProperty('tl_test', 'test');
	}

	public function tearDown()
	{
		$this->objProperty = null;
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

	public function testGetName()
	{
		$this->assertEquals('test', $this->objProperty->getName());
	}

	public function testAsArray()
	{
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['fields']['test'], $this->objProperty->asArray());
	}

	public function testAsString()
	{
		$this->assertEquals($this->objProperty->getName(), $this->objProperty->asString());
	}

	public function testIsSelector()
	{
		$this->assertFalse($this->objProperty->isSelector());
		$this->assertTrue($this->objProperty->isSelector(true));
		$this->assertTrue(in_array('test', $GLOBALS['TL_DCA']['tl_test']['palettes']['__selector__']));

		$objProperty = $this->objProperty->getDataContainer()->getProperty('done');

		$this->assertTrue($objProperty->isSelector());
		$this->assertFalse($objProperty->isSelector(false));
		$this->assertFalse(in_array('done', $GLOBALS['TL_DCA']['tl_test']['palettes']['__selector__']));
	}

	public function testGetters()
	{
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['fields']['test']['label'], $this->objProperty->getLabel());
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['fields']['test']['inputType'], $this->objProperty->getWidgetType());
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['fields']['test']['eval'], $this->objProperty->getEvaluation());
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['fields']['test']['eval']['tl_class'], $this->objProperty->getEvaluationAttribute('tl_class'));

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['fields']['test']['filter'], $this->objProperty->isFilterable());
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['fields']['test']['search'], $this->objProperty->isSearchable());
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['fields']['test']['sorting'], $this->objProperty->isSortable());

		$this->assertEquals($this->objProperty->get('sorting'), $this->objProperty->isSortable());
		$this->assertEquals($this->objProperty->get('sorting'), $this->objProperty->getFromDefinition('sorting'));

		$this->assertEquals($this->objProperty->getDataContainer(), $this->objProperty->getParent());
	}

	public function testSetWidgetType()
	{
		$this->assertEquals($this->objProperty, $this->objProperty->setWidgetType('select'));
		$this->assertEquals('select', $this->objProperty->getWidgetType());
		$this->assertEquals('select', $GLOBALS['TL_DCA']['tl_test']['fields']['test']['inputType']);
	}

	public function testSetLabel()
	{
		$this->assertEquals($this->objProperty, $this->objProperty->setLabel(array('Holla', 'hoop')));
		$this->assertEquals(array('Holla', 'hoop'), $this->objProperty->getLabel());
		$this->assertEquals(array('Holla', 'hoop'), $GLOBALS['TL_DCA']['tl_test']['fields']['test']['label']);
	}

	public function testSetFilterable()
	{
		$this->assertEquals($this->objProperty, $this->objProperty->setFilterable(false));
		$this->assertEquals(false, $this->objProperty->isFilterable());
		$this->assertEquals(false, $GLOBALS['TL_DCA']['tl_test']['fields']['test']['filter']);

		$this->assertEquals($this->objProperty, $this->objProperty->setFilterable(true));
		$this->assertEquals(true, $this->objProperty->isFilterable());
		$this->assertEquals(true, $GLOBALS['TL_DCA']['tl_test']['fields']['test']['filter']);
	}

	public function testSetSearchable()
	{
		$this->assertEquals($this->objProperty, $this->objProperty->setSearchable(false));
		$this->assertEquals(false, $this->objProperty->isSearchable());
		$this->assertEquals(false, $GLOBALS['TL_DCA']['tl_test']['fields']['test']['search']);

		$this->assertEquals($this->objProperty, $this->objProperty->setSearchable(true));
		$this->assertEquals(true, $this->objProperty->isSearchable());
		$this->assertEquals(true, $GLOBALS['TL_DCA']['tl_test']['fields']['test']['search']);
	}

	public function testIsSortable()
	{
		$this->assertEquals($this->objProperty, $this->objProperty->setSortable(false));
		$this->assertEquals(false, $this->objProperty->isSortable());
		$this->assertEquals(false, $GLOBALS['TL_DCA']['tl_test']['fields']['test']['search']);

		$this->assertEquals($this->objProperty, $this->objProperty->setSortable(true));
		$this->assertEquals(true, $this->objProperty->isSortable());
	}

	public function testSetLabelByRef()
	{
		$arrLabel = array('Foo', 'Bar');
		$this->assertEquals($this->objProperty, $this->objProperty->setLabelByRef($arrLabel));

		$this->assertEquals($arrLabel, $this->objProperty->getLabel());
		$this->assertEquals($arrLabel, $GLOBALS['TL_DCA']['tl_test']['fields']['test']['label']);

		$arrLabel = array('Foo', 'bar2');
		$this->assertEquals($arrLabel, $this->objProperty->getLabel());
		$this->assertEquals($arrLabel, $GLOBALS['TL_DCA']['tl_test']['fields']['test']['label']);
	}

	public function testHasSubPalette()
	{
		$this->assertFalse($this->objProperty->hasSubPalette());

		$GLOBALS['TL_DCA']['tl_test']['subpalettes']['test'] = array();
		$this->assertFalse($this->objProperty->hasSubPalette());

		$GLOBALS['TL_DCA']['tl_test']['subpalettes']['test_2'] = array();
		$this->assertFalse($this->objProperty->hasSubPalette('2'));

		$GLOBALS['TL_DCA']['tl_test']['palettes']['__selector__'][] = 'test';
		$this->assertTrue($this->objProperty->hasSubPalette('2'));
		$this->assertTrue($this->objProperty->hasSubPalette());
	}

	public function testSetEvaluationAttribute()
	{
		$this->assertEquals($this->objProperty, $this->objProperty->setEvaluationAttribute('tl_class', 'foo'));
		$this->assertEquals('foo', $this->objProperty->getEvaluationAttribute('tl_class'));
		$this->assertEquals('foo', $GLOBALS['TL_DCA']['tl_test']['fields']['test']['eval']['tl_class']);
	}

	public function testSet()
	{
		$this->assertEquals($this->objProperty, $this->objProperty->set('label', 'fooset'));
		$this->assertEquals('fooset', $this->objProperty->getLabel());
		$this->assertEquals('fooset', $GLOBALS['TL_DCA']['tl_test']['fields']['test']['label']);
	}

	public function testCopy()
	{
		$objNew = $this->objProperty->copy('neu');

		$this->assertInstanceOf('DcaTools\Definition\Property', $objNew);

		$this->assertEquals('neu', $objNew->getName());
		$this->assertTrue(isset($GLOBALS['TL_DCA']['tl_test']['fields']['neu']));
		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['fields']['neu'], $GLOBALS['TL_DCA']['tl_test']['fields']['test']);

		$objNew->set('label', array('no', 'thing'));
		$this->assertNotEquals($GLOBALS['TL_DCA']['tl_test']['fields']['neu'], $GLOBALS['TL_DCA']['tl_test']['fields']['test']);
	}

	public function testExtends()
	{
		$objDc = $this->objProperty->getDataContainer();
		$objNew = $objDc->createProperty('neu');

		$this->assertEquals($objNew, $objNew->extend($this->objProperty));
		$this->assertEquals($objNew->getLabel(), $this->objProperty->getLabel());
		$this->assertEquals($objNew->getEvaluation(), $this->objProperty->getEvaluation());
		$this->assertEquals($objNew->getParent(), $this->objProperty->getParent());
		$this->assertEquals($objNew->isSearchable(), $this->objProperty->isSearchable());
		$this->assertEquals($objNew->isSortable(), $this->objProperty->isSortable());
		$this->assertEquals($objNew->isFilterable(), $this->objProperty->isFilterable());

		$this->assertNotEquals($objNew->getName(), $this->objProperty->getName());

		$this->assertNotEmpty($GLOBALS['TL_DCA']['tl_test']['fields']['neu'], $GLOBALS['TL_DCA']['tl_test']['fields']['test']);
	}

	public function testRemove()
	{
		$objDc = $this->objProperty->getDataContainer();
		$this->objProperty->remove();

		$this->assertFalse($objDc->hasProperty('test'));
		$this->assertFalse(isset($GLOBALS['TL_DCA']['tl_test']['fields']['test']));
	}
}
