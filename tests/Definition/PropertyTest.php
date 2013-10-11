<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 11.10.13
 * Time: 13:19
 * To change this template use File | Settings | File Templates.
 */

use DcaTools\Definition;

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

		$obj         = new Definition();
		$refObject   = new ReflectionObject( $obj );
		$refProperty = $refObject->getProperty( 'arrDataContainers' );
		$refProperty->setAccessible( true );
		$refProperty->setValue(null, array());
	}

	public function testGetName()
	{
		$this->assertEquals('test', $this->objProperty->getName());
	}
}