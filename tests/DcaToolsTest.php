<?php
/**
 * Created by JetBrains PhpStorm.
 * User: david
 * Date: 30.09.13
 * Time: 18:09
 * To change this template use File | Settings | File Templates.
 */

require_once dirname(__FILE__) . '/bootstrap.php';

use \Netzmacht\DcaTools\DataContainer;
use \Netzmacht\DcaTools\DcaTools;

$GLOBALS['TL_DCA']['tl_test'] = array();

class DcaToolsTest extends PHPUnit_Framework_TestCase
{
	protected $objDataContainer;

	public function setUp()
	{
		$this->initializeTlTest();
		$this->objDataContainer = new DataContainer('tl_test');
	}

	protected function initializeTlTest()
	{
		$GLOBALS['TL_DCA']['tl_test'] = array
		(
			'config' => array(),
			'propertys' => array()
		);
	}

	public function tearDown()
	{
		$this->objDataContainer = null;
		unset($GLOBALS['TL_DCA']['tl_test']);

		$obj         = new DcaTools();
		$refObject   = new ReflectionObject( $obj );
		$refProperty = $refObject->getProperty( 'arrDataContainers' );
		$refProperty->setAccessible( true );
		$refProperty->setValue(null, array());
	}


	public function testGetDataContainer()
	{
		$this->assertEquals($this->objDataContainer, DcaTools::getDataContainer('tl_test'));
	}

	public function testDoAutoUpdate()
	{
		$this->assertFalse(DcaTools::doAutoUpdate());

		DcaTools::doAutoUpdate(true);
		$this->assertTrue(DcaTools::doAutoUpdate());

		DcaTools::doAutoUpdate(false);
		$this->assertFalse(DcaTools::doAutoUpdate());

		$this->assertTrue(DcaTools::doAutoUpdate(true));
		$this->assertFalse(DcaTools::doAutoUpdate(false));
	}

	public function testRegisterListener()
	{
		$objDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();

		DcaTools::registerListener($objDispatcher, 'test', array('Foo', 'bar'));
		$this->assertEquals($objDispatcher->getListeners('test'), array(array('Foo', 'bar')));

		$objDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();

		DcaTools::registerListener($objDispatcher, 'test', array('Test', 'Case'));
		DcaTools::registerListener($objDispatcher, 'test', array(array('Foo', 'bar'), 1));

		$this->assertEquals($objDispatcher->getListeners('test'), array(array('Foo', 'bar'), array('Test', 'Case')));

		$objDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();

		DcaTools::registerListener($objDispatcher, 'test', array('Foo', 'bar', array('test' => 'case')));
		$listener = $objDispatcher->getListeners('test');
		$this->assertTrue($listener[0] instanceof Closure);
	}

}