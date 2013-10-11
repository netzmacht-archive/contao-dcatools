<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 30.09.13
 * Time: 18:09
 * To change this template use File | Settings | File Templates.
 */

require_once dirname(__FILE__) . '/bootstrap.php';

use DcaTools\Definition\DataContainer;
use DcaTools\Definition;

$GLOBALS['TL_DCA']['tl_test'] = array();

class DefinitionTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var DataContainer
	 */
	protected $objDataContainer;

	public function setUp()
	{
		$GLOBALS['TL_DCA']['tl_test'] = $GLOBALS['TEST_DCA'];
		$this->objDataContainer = Definition::getDataContainer('tl_test');
	}

	public function tearDown()
	{
		$this->objDataContainer = null;
		unset($GLOBALS['TL_DCA']['tl_test']);

		$refObject   = new ReflectionObject( new Definition() );
		$refProperty = $refObject->getProperty( 'arrDataContainers' );
		$refProperty->setAccessible( true );
		$refProperty->setValue(null, array());
	}

	public function testGetDataContainer()
	{
		$this->assertInstanceOf('DcaTools\Definition\DataContainer', Definition::getDataContainer('tl_test'));
	}

	public function testGetPalette()
	{
		$this->assertInstanceOf('DcaTools\Definition\Palette', Definition::getPalette('tl_test', 'default'));
		$this->assertEquals($this->objDataContainer->getPalette('default'), Definition::getPalette('tl_test', 'default'));
	}

	public function testGetOperation()
	{
		$this->assertInstanceOf('DcaTools\Definition\Operation', Definition::getOperation('tl_test', 'edit'));
		$this->assertEquals($this->objDataContainer->getOperation('edit'), Definition::getOperation('tl_test', 'edit'));

		$this->assertInstanceOf('DcaTools\Definition\Operation', Definition::getOperation('tl_test', 'sub', 'global'));
		$this->assertEquals($this->objDataContainer->getOperation('sub', 'global'), Definition::getOperation('tl_test', 'sub', 'global'));
	}

	public function testGetSubPalette()
	{
		$this->assertInstanceOf('DcaTools\Definition\SubPalette', Definition::getSubPalette('tl_test', 'sub'));
		$this->assertEquals($this->objDataContainer->getSubPalette('sub'), Definition::getSubPalette('tl_test', 'sub'));
	}

	public function testGetProperty()
	{
		$this->assertInstanceOf('DcaTools\Definition\Property', Definition::getProperty('tl_test', 'test'));
		$this->assertEquals($this->objDataContainer->getProperty('test'), Definition::getProperty('tl_test', 'test'));
	}
}