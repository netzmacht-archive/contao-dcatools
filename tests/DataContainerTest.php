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
use \Netzmacht\DcaTools\Property;

$GLOBALS['TL_DCA']['tl_test'] = array();

class DataContainerTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var DataContainer
	 */
	protected $objDataContainer;

	public function setUp()
	{
		$this->initializeTlTest();
		DcaTools::doAutoUpdate(false);
		$this->objDataContainer = DcaTools::getDataContainer('tl_test');
	}

	protected function initializeTlTest()
	{
		$GLOBALS['TL_DCA']['tl_test'] = array
		(
			'config' => array
			(
			),

			'propertys' => array
			(
				'test' => array
				(
					'label' => array('Test', 'Label'),
					'inputType' => 'text',
				)
			)
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
		$this->assertEquals($this->objDataContainer, $this->objDataContainer->getDataContainer());
	}

	public function testGetRecord()
	{
		$this->assertNull($this->objDataContainer->getRecord());

		$objModel = new ContentModel();
		$this->objDataContainer->setRecord($objModel);
		$this->assertEquals($objModel, $this->objDataContainer->getRecord());

		$objDataContainer = new DataContainer('tl_test', $objModel);
		$this->assertEquals($objModel, $objDataContainer->getRecord());
	}

	public function testSetRecord()
	{
		// Contao SUCKS, cannot test against the same model
		$objModel = ArticleModel::findAll(array('limit' => 1, 'return' => 'Model'));
		$this->assertEquals($this->objDataContainer->setRecord($objModel), $this->objDataContainer);
		$this->assertEquals($objModel, $this->objDataContainer->getRecord($objModel));

		$objCollection = PageModel::findAll(array('limit' => 2));

		$this->objDataContainer->setRecord($objCollection);
		$this->assertEquals($objCollection->current(), $this->objDataContainer->getRecord());

		$objResult = \Database::getInstance()->query('SELECT * FROM tl_content limit 2');
		$this->objDataContainer->setRecord($objResult);
		$this->assertEquals($objResult, $this->objDataContainer->getRecord());
	}

	public function testHasRecord()
	{
		$this->assertFalse($this->objDataContainer->hasRecord());

		$objModel = new StyleModel();
		$this->objDataContainer->setRecord($objModel);
		$this->assertTrue($this->objDataContainer->hasRecord());
	}


	public function testGetProperty()
	{
		$objProperty = new Property('test', $this->objDataContainer);
		$objProperty->addListener('delete', array($this->objDataContainer, 'propertyListener'));

		$this->assertEquals($objProperty, $this->objDataContainer->getProperty('test'));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetPropertyException()
	{
		$this->objDataContainer->getProperty('notexisting');
	}

	public function testHasProperty()
	{
		$this->assertFalse($this->objDataContainer->hasProperty('notexisting'));
		$this->assertTrue($this->objDataContainer->hasProperty('test'));
	}

	public function testRemoveProperty()
	{
		$this->assertTrue($this->objDataContainer->hasProperty('test'));

		$this->objDataContainer->removeProperty('test');
		$this->assertFalse($this->objDataContainer->hasProperty('test'));
	}

	public function testCreateProperty()
	{
		$this->assertFalse($this->objDataContainer->hasProperty('new'));

		$objProperty = $this->objDataContainer->createProperty('new');

		$this->assertTrue($this->objDataContainer->hasProperty('new'));
		$this->assertEquals($objProperty, $this->objDataContainer->getProperty('new'));
	}
}