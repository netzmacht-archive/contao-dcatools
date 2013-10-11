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
		$this->objDataContainer = new DataContainer('tl_test');
	}

	public function tearDown()
	{
		$this->objDataContainer = null;
		unset($GLOBALS['TL_DCA']['tl_test']);

		$obj         = new Definition();
		$refObject   = new ReflectionObject( $obj );
		$refProperty = $refObject->getProperty( 'arrDataContainers' );
		$refProperty->setAccessible( true );
		$refProperty->setValue(null, array());
	}


	public function testGetDataContainer()
	{
		$this->assertEquals($this->objDataContainer, Definition::getDataContainer('tl_test'));
	}

	public function testGetOperation()
	{
		$this->assertInstanceOf('DcaTools\Definition\Operation', Definition::getDataContainer('edit'));
		$this->assertEquals($this->objDataContainer->getOperation('edit'), Definition::getDataContainer('edit'));

		$this->assertInstanceOf('DcaTools\Definition\GlobalOperation', Definition::getDataContainer('sub', 'global'));
		$this->assertEquals($this->objDataContainer->getOperation('sub', 'global'), Definition::getDataContainer('sub', 'global'));
	}
}