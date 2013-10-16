<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 16.10.13
 * Time: 16:17
 * To change this template use File | Settings | File Templates.
 */

use DcaTools\Definition;

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
}