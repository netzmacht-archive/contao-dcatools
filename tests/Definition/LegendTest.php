<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 16.10.13
 * Time: 16:17
 * To change this template use File | Settings | File Templates.
 */

use deprecated\DcaTools\Definition;

class LegendTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var Definition\Legend
	 */
	protected $objLegend;

	public function setUp()
	{
		$GLOBALS['TL_DCA']['tl_test'] = $GLOBALS['TEST_DCA'];
		$this->objLegend = Definition::getPalette('tl_test', 'default')->getLegend('title');
	}

	public function tearDown()
	{
		$this->objLegend = null;
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
		$this->assertEquals(array('test'), $this->objLegend->getPropertyNames());
	}

	public function testGetProperties()
	{
		$this->assertEquals(array('test' => $this->objLegend->getProperty('test')), $this->objLegend->getProperties());
	}

	public function testHasProperty()
	{
		$this->assertTrue($this->objLegend->hasProperty('test'));
		$this->assertFalse($this->objLegend->hasProperty('done'));
	}

	public function testGetProperty()
	{
		$this->assertInstanceOf('DcaTools\Definition\Property', $this->objLegend->getProperty('test'));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetPropertyException()
	{
		$this->objLegend->getProperty('done');
	}

	public function testAddProperty()
	{
		$objProperty = $this->objLegend->getDataContainer()->getProperty('done');

		$objProperty = $this->objLegend->addProperty($objProperty);
		$this->assertInstanceOf('DcaTools\Definition\Property', $objProperty);

		$this->assertTrue($this->objLegend->hasProperty('done'));
		$this->assertEquals($this->objLegend->getPropertyNames(), array_intersect(
			$this->objLegend->getPropertyNames(),
			$this->objLegend->getPalette()->getPropertyNames()
		));

		$this->assertEquals('{title_legend},test,done', $this->objLegend->asString());
		$this->assertTrue(strpos($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], 'test,done') !== false);
	}

	public function testRemoveProperty()
	{
		$this->assertEquals($this->objLegend, $this->objLegend->removeProperty('notexisting'));
		$this->assertEquals($this->objLegend, $this->objLegend->removeProperty('test'));

		$this->assertFalse($this->objLegend->hasProperty('test'));
		$this->assertTrue($this->objLegend->getDataContainer()->hasProperty('test'));

		$this->objLegend->removeProperty('test', true);
		$this->assertTrue($this->objLegend->getDataContainer()->hasProperty('test'));

		$this->objLegend->addProperty('test');
		$this->objLegend->removeProperty('test', true);
		$this->assertFalse($this->objLegend->getDataContainer()->hasProperty('test'));

		$this->assertEquals($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '');
	}

	public function testMoveProperty()
	{
		$objProperty = $this->objLegend->getDataContainer()->getProperty('done');
		$this->objLegend->addProperty($objProperty, 'title');

		$this->assertTrue(strpos($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], 'test,done') !== false);

		$this->objLegend->moveProperty($objProperty, 'test', Definition::BEFORE);
		$this->assertTrue(strpos($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], 'done,test') !== false);

		$this->objLegend->moveProperty($objProperty, 'test', Definition::AFTER);
		$this->assertTrue(strpos($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], 'test,done') !== false);

		$this->objLegend->moveProperty($objProperty, 'test', Definition::FIRST);
		$this->assertTrue(strpos($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], 'done,test') !== false);

		$this->objLegend->moveProperty($objProperty, 'test', Definition::LAST);
		$this->assertTrue(strpos($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], 'test,done') !== false);

		$this->objLegend->moveProperty($objProperty, Definition::FIRST);
		$this->assertTrue(strpos($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], 'done,test') !== false);

		$this->objLegend->moveProperty($objProperty, Definition::LAST);
		$this->assertTrue(strpos($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], 'test,done') !== false);
	}

	public function testCreateProperty()
	{
		$objProperty = $this->objLegend->createProperty('name');

		$this->assertInstanceOf('DcaTools\Definition\Property', $objProperty);
		$this->assertTrue($this->objLegend->getDataContainer()->hasProperty('name'));

		$this->assertTrue(strpos($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], 'test,name') !== false);

		$objProperty = $this->objLegend->createProperty('new');
		$this->assertInstanceOf('DcaTools\Definition\Property', $objProperty);
		$this->assertTrue($this->objLegend->getDataContainer()->hasProperty('new'));

		$this->assertTrue(strpos($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], 'test,name,new') !== false);
	}

	public function testHasSelectors()
	{
		$this->objLegend->addProperty('done');
		$this->assertTrue($this->objLegend->hasSelectors());

		$this->objLegend->removeProperty('done');
		$this->assertFalse($this->objLegend->hasSelectors());

		$this->objLegend->addProperty('done');
		$this->assertTrue($this->objLegend->hasSelectors());

		unset($GLOBALS['TL_DCA']['tl_test']['palettes']['__selector__']);
		$this->assertFalse($this->objLegend->hasSelectors());
	}

	public function testGetSelectors()
	{
		$this->assertEmpty($this->objLegend->getSelectors());

		$this->objLegend->addProperty('done');
		$this->assertArrayHasKey('done', $this->objLegend->getSelectors());
	}

	public function testExport()
	{
		$this->assertEquals('{title_legend},test', $this->objLegend->asString());
		$this->assertEquals(array('test'), $this->objLegend->asArray());
	}

	public function testRemove()
	{
		$objDc = $this->objLegend->getPalette();

		$this->assertEquals($this->objLegend, $this->objLegend->remove());

		$this->assertNull($this->objLegend->getPalette());
		$this->assertFalse($objDc->hasLegend('title'));

		$this->assertTrue(strpos($GLOBALS['TL_DCA']['tl_test']['palettes']['default'], '{sub') === false);
	}

	public function testExtend()
	{
		$objLegend = $this->objLegend->getPalette()->createLegend('second');
		$objLegend->extend($this->objLegend);

		$this->assertEquals($objLegend->asArray(), $this->objLegend->asArray());

		$objLegend->createProperty('final');
		$this->assertNotEquals($objLegend->asArray(), $this->objLegend->asArray());
	}
	
}